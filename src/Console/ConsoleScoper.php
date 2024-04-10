<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoper\Console;

use Fidry\Console\Application\Application;
use Fidry\Console\IO;
use Humbug\PhpScoper\Autoload\ComposerFileHasher;
use Humbug\PhpScoper\Autoload\ScoperAutoloadGenerator;
use Humbug\PhpScoper\Configuration\Configuration;
use Humbug\PhpScoper\Scoper\Scoper;
use Humbug\PhpScoper\Scoper\ScoperFactory;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use Humbug\PhpScoper\Throwable\Exception\ParsingException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Throwable;
use Webmozart\Assert\Assert;
use function array_keys;
use function array_map;
use function array_values;
use function count;
use function dirname;
use function preg_match as native_preg_match;
use function Safe\fileperms;
use function str_replace;
use function strlen;
use function usort;
use const DIRECTORY_SEPARATOR;

/**
 * @private
 */
final readonly class ConsoleScoper
{
    private const VENDOR_DIR_PATTERN = '~((?:.*)\\'.DIRECTORY_SEPARATOR.'vendor)\\'.DIRECTORY_SEPARATOR.'.*~';

    public function __construct(
        private Filesystem $fileSystem,
        private Application $application,
        private ScoperFactory $scoperFactory,
    ) {
    }

    /**
     * @param list<non-empty-string> $paths
     * @param non-empty-string       $outputDir
     */
    public function scope(
        IO $io,
        Configuration $config,
        array $paths,
        string $outputDir,
        bool $stopOnFailure
    ): void {
        $logger = new ScoperLogger(
            $this->application,
            $io,
        );

        $logger->outputScopingStart(
            $config->getPrefix(),
            $paths,
        );

        try {
            $this->doScope(
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

    private function doScope(
        Configuration $config,
        string $outputDir,
        bool $stopOnFailure,
        ScoperLogger $logger
    ): void {
        // Creates output directory if does not already exist
        $this->fileSystem->mkdir($outputDir);

        [$files, $excludedFilesWithContents] = self::getFiles($config, $outputDir);

        $symbolsRegistry = new SymbolsRegistry();

        $this->scopeFiles(
            $config,
            $files,
            $stopOnFailure,
            $logger,
            $symbolsRegistry,
        );

        foreach ($excludedFilesWithContents as $excludedFileWithContent) {
            $this->dumpFileWithPermissions($excludedFileWithContent);
        }

        self::dumpScoperAutoloader(
            $files,
            $excludedFilesWithContents,
            $symbolsRegistry,
        );
    }

    /**
     * @param File[] $files
     * @param File[] $excludedFilesWithContents
     */
    private function dumpScoperAutoloader(
        array $files,
        array $excludedFilesWithContents,
        SymbolsRegistry $symbolsRegistry,
    ): void {
        $excludeFileInputPaths = self::mapFilesToInputPath($excludedFilesWithContents);

        $scopedFilesVendorDir = self::findVendorDir(
            [
                ...self::mapFilesToOutputPath($files),
                ...self::mapFilesToOutputPath($excludedFilesWithContents),
            ],
        );
        $sourceVendorDir = self::findVendorDir(
            [
                ...self::mapFilesToInputPath($files),
                ...$excludeFileInputPaths,
            ],
        );

        if (null === $scopedFilesVendorDir || null === $sourceVendorDir) {
            return;
        }

        $sourceRootDir = dirname($sourceVendorDir);

        $fileHashGenerator = ComposerFileHasher::create(
            $sourceVendorDir,
            $sourceRootDir,
            $excludeFileInputPaths,
        );
        $fileHashes = $fileHashGenerator->generateHashes();

        $autoload = (new ScoperAutoloadGenerator($symbolsRegistry, $fileHashes))->dump();

        $this->fileSystem->dumpFile(
            $scopedFilesVendorDir.DIRECTORY_SEPARATOR.'scoper-autoload.php',
            $autoload,
        );
    }

    /**
     * @param File[] $files
     */
    private function scopeFiles(
        Configuration $config,
        array $files,
        bool $stopOnFailure,
        ScoperLogger $logger,
        SymbolsRegistry $symbolsRegistry,
    ): void {
        $logger->outputFileCount(count($files));

        $scoper = $this->scoperFactory->createScoper(
            $config,
            $symbolsRegistry,
        );

        foreach ($files as $file) {
            $this->scopeFile(
                $scoper,
                $file,
                $stopOnFailure,
                $logger,
            );
        }
    }

    private function dumpFileWithPermissions(File $file): void
    {
        $outputFilePath = $file->outputFilePath;

        $this->fileSystem->dumpFile($outputFilePath, $file->inputContents);

        $originalFilePermissions = fileperms($file->inputFilePath) & 0o777;

        if ($originalFilePermissions !== 420) {
            // Only change the permissions if necessary
            $this->fileSystem->chmod($outputFilePath, $originalFilePermissions);
        }
    }

    /**
     * @param  File[]   $files
     * @return string[]
     */
    private static function mapFilesToInputPath(array $files): array
    {
        return array_map(
            static fn (File $file) => $file->inputFilePath,
            $files,
        );
    }

    /**
     * @param  File[]   $files
     * @return string[]
     */
    private static function mapFilesToOutputPath(array $files): array
    {
        return array_map(
            static fn (File $file) => $file->outputFilePath,
            $files,
        );
    }

    /**
     * @return array{list<File>, list<File>}
     */
    private static function getFiles(Configuration $config, string $outputDir): array
    {
        $filesWithContent = $config->getFilesWithContents();
        $excludedFilesWithContents = $config->getExcludedFilesWithContents();

        $commonDirectoryPath = Path::getLongestCommonBasePath(
            ...array_map(
                Path::getDirectory(...),
                array_keys($filesWithContent),
            ),
            ...array_map(
                Path::getDirectory(...),
                array_keys($excludedFilesWithContents),
            ),
        );
        Assert::notNull($commonDirectoryPath);

        $mapFiles = static fn (array $inputFileTuple) => new File(
            Path::normalize($inputFileTuple[0]),
            $inputFileTuple[1],
            $outputDir.str_replace($commonDirectoryPath, '', Path::normalize($inputFileTuple[0])),
        );

        return [
            array_values(
                array_map(
                    $mapFiles,
                    $filesWithContent,
                ),
            ),
            array_values(
                array_map(
                    $mapFiles,
                    $excludedFilesWithContents,
                ),
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
            static fn ($a, $b) => strlen((string) $a) <=> strlen((string) $b),
        );

        return (0 === count($vendorDirs)) ? null : (string) $vendorDirs[0];
    }

    private function scopeFile(
        Scoper $scoper,
        File $file,
        bool $stopOnFailure,
        ScoperLogger $logger
    ): void {
        $successfullyScoped = false;
        $inputFilePath = $file->inputFilePath;

        try {
            $scoppedContent = $scoper->scope(
                $inputFilePath,
                $file->inputContents,
            );

            $successfullyScoped = true;
        } catch (ParsingException $parsingException) {
            $logger->outputWarnOfFailure($inputFilePath, $parsingException);

            // Fallback on unchanged content
            $scoppedContent = $file->inputContents;
        } catch (Throwable $throwable) {
            $exception = ParsingException::forFile(
                $inputFilePath,
                $throwable,
            );

            if ($stopOnFailure) {
                throw $exception;
            }

            $logger->outputWarnOfFailure($inputFilePath, $exception);

            // Fallback on unchanged content
            $scoppedContent = $file->inputContents;
        }

        $this->dumpFileWithPermissions(
            $file->withScoppedContent($scoppedContent),
        );

        if ($successfullyScoped) {
            $logger->outputSuccess($inputFilePath);
        }
    }
}
