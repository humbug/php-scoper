<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Console;

use Fidry\Console\Application\Application;
use Fidry\Console\IO;
use Humbug\PhpScoper\Autoload\ScoperAutoloadGenerator;
use Humbug\PhpScoper\Configuration;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Throwable\Exception\ParsingException;
use Humbug\PhpScoper\Whitelist;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;
use function array_column;
use function array_keys;
use function array_map;
use function count;
use function Humbug\PhpScoper\get_common_path;
use function preg_match as native_preg_match;
use function Safe\file_get_contents;
use function Safe\sprintf;
use function Safe\usort;
use function str_replace;
use function strlen;
use const DIRECTORY_SEPARATOR;

/**
 * @private
 */
final class ConsoleScoper
{
    private const VENDOR_DIR_PATTERN = '~((?:.*)\\'.DIRECTORY_SEPARATOR.'vendor)\\'.DIRECTORY_SEPARATOR.'.*~';

    private Filesystem $fileSystem;
    private Application $application;
    private Scoper $scoper;

    public function __construct(
        Filesystem $fileSystem,
        Application $application,
        Scoper $scoper
    )
    {
        $this->fileSystem = $fileSystem;
        $this->application = $application;
        $this->scoper = $scoper;
    }

    public function scope(
        IO $io,
        Configuration $config,
        array $paths,
        string $outputDir,
        bool $stopOnFailure
    ): void
    {
        $logger = new ScoperLogger(
            $this->application,
            $io,
        );

        $logger->outputScopingStart(
            $config->getPrefix(),
            $paths,
        );

        try {
            $this->scopeFiles(
                $config,
                $outputDir,
                $stopOnFailure,
                $logger,
            );
        } catch (Throwable $throwable) {
            $this->fileSystem->remove($outputDir);

            $logger->outputScopingEndWithFailure();

            throw $throwable;
        }

        $logger->outputScopingEnd();
    }

    private function scopeFiles(
        Configuration $config,
        string $outputDir,
        bool $stopOnFailure,
        ScoperLogger $logger
    ): void {
        // Creates output directory if does not already exist
        $this->fileSystem->mkdir($outputDir);

        $files = self::getFiles($config, $outputDir);

        $logger->outputFileCount(count($files));

        foreach ($files as [$inputFilePath, $inputContents, $outputFilePath]) {
            $this->scopeFile(
                $inputFilePath,
                $inputContents,
                $outputFilePath,
                $config,
                $stopOnFailure,
                $logger,
            );
        }

        $vendorDir = self::findVendorDir(array_column($files, 2));

        if (null !== $vendorDir) {
            $autoload = (new ScoperAutoloadGenerator($config->getWhitelist()))->dump();

            $this->fileSystem->dumpFile(
                $vendorDir.DIRECTORY_SEPARATOR.'scoper-autoload.php',
                $autoload,
            );
        }
    }

    /**
     * @return array<array{string, string, string}>
     */
    private static function getFiles(Configuration $config, string $outputDir): array
    {
        $filesWithContent = $config->getFilesWithContents();
        $commonPath = get_common_path(array_keys($filesWithContent));

        return array_map(
            static fn (array $inputFileTuple) => [
                $inputFileTuple[0],
                $inputFileTuple[1],
                $outputDir.str_replace($commonPath, '', $inputFileTuple[0]),
            ],
            $filesWithContent,
        );
    }

    private static function findVendorDir(array $outputFilePaths): ?string
    {
        $vendorDirsAsKeys = [];

        foreach ($outputFilePaths as $filePath) {
            if (native_preg_match(self::VENDOR_DIR_PATTERN, $filePath, $matches)) {
                $vendorDirsAsKeys[$matches[1]] = true;
            }
        }

        $vendorDirs = array_keys($vendorDirsAsKeys);

        usort(
            $vendorDirs,
            static fn ($a, $b) => strlen($b) <=> strlen($a),
        );

        return (0 === count($vendorDirs)) ? null : $vendorDirs[0];
    }

    /**
     * @param callable[] $patchers
     */
    private function scopeFile(
        string $inputFilePath,
        string $inputContents,
        string $outputFilePath,
        Configuration $config,
        bool $stopOnFailure,
        ScoperLogger $logger
    ): void {
        try {
            $scoppedContent = $this->scoper->scope(
                $inputFilePath,
                $inputContents,
                $config->getPrefix(),
                $config->getPatchers(),
                $config->getWhitelist(),
            );
        } catch (Throwable $throwable) {
            $exception = new ParsingException(
                sprintf(
                    'Could not parse the file "%s".',
                    $inputFilePath
                ),
                0,
                $throwable,
            );

            if ($stopOnFailure) {
                throw $exception;
            }

            $logger->outputWarnOfFailure($inputFilePath, $exception);

            // Fallback on unchanged content
            $scoppedContent = file_get_contents($inputFilePath);
        }

        $this->fileSystem->dumpFile($outputFilePath, $scoppedContent);

        if (false === isset($exception)) {
            $logger->outputSuccess($inputFilePath);
        }
    }
}
