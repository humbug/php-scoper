<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Console;

use Fidry\Console\Application\Application;
use Fidry\Console\IO;
use Humbug\PhpScoper\Autoload\ScoperAutoloadGenerator;
use Humbug\PhpScoper\Configuration\Configuration;
use Humbug\PhpScoper\Scoper\Scoper;
use Humbug\PhpScoper\Scoper\ScoperFactory;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use Humbug\PhpScoper\Throwable\Exception\ParsingException;
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
    private ScoperFactory $scoperFactory;

    public function __construct(
        Filesystem $fileSystem,
        Application $application,
        ScoperFactory $scoperFactory
    )
    {
        $this->fileSystem = $fileSystem;
        $this->application = $application;
        $this->scoperFactory = $scoperFactory;
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

        [$files, $whitelistedFiles] = self::getFiles($config, $outputDir);

        $logger->outputFileCount(count($files));

        $symbolsRegistry = new SymbolsRegistry();

        $scoper = $this->scoperFactory->createScoper(
            $config,
            $symbolsRegistry,
        );

        foreach ($files as [$inputFilePath, $inputContents, $outputFilePath]) {
            $this->scopeFile(
                $scoper,
                $inputFilePath,
                $inputContents,
                $outputFilePath,
                $stopOnFailure,
                $logger,
            );
        }

        foreach ($whitelistedFiles as [$inputFilePath, $inputContents, $outputFilePath]) {
            $this->fileSystem->dumpFile($outputFilePath, $inputContents);
        }

        $vendorDir = self::findVendorDir(
            [
                ...array_column($files, 2),
                ...array_column($whitelistedFiles, 2),
            ],
        );

        if (null !== $vendorDir) {
            $autoload = (new ScoperAutoloadGenerator($symbolsRegistry))->dump();

            $this->fileSystem->dumpFile(
                $vendorDir.DIRECTORY_SEPARATOR.'scoper-autoload.php',
                $autoload,
            );
        }
    }

    /**
     * @return array{array<array{string, string, string}>, array<array{string, string, string}>}
     */
    private static function getFiles(Configuration $config, string $outputDir): array
    {
        $filesWithContent = $config->getFilesWithContents();
        $excludedFilesWithContents = $config->getExcludedFilesWithContents();

        $commonPath = get_common_path(
            [
                ...array_keys($filesWithContent),
                ...array_keys($excludedFilesWithContents),
            ],
        );

        $mapFiles = static fn (array $inputFileTuple) => [
            $inputFileTuple[0],
            $inputFileTuple[1],
            $outputDir.str_replace($commonPath, '', $inputFileTuple[0]),
        ];

        return [
            array_map(
                $mapFiles,
                $filesWithContent,
            ),
            array_map(
                $mapFiles,
                $excludedFilesWithContents,
            ),
        ];
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
            static fn ($a, $b) => strlen($a) <=> strlen($b),
        );

        return (0 === count($vendorDirs)) ? null : $vendorDirs[0];
    }

    private function scopeFile(
        Scoper $scoper,
        string $inputFilePath,
        string $inputContents,
        string $outputFilePath,
        bool $stopOnFailure,
        ScoperLogger $logger
    ): void {
        try {
            $scoppedContent = $scoper->scope(
                $inputFilePath,
                $inputContents,
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

        if (!isset($exception)) {
            $logger->outputSuccess($inputFilePath);
        }
    }
}
