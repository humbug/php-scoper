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
use Fidry\Console\Input\IO;
use Humbug\PhpScoper\Autoload\FileHashGenerator;
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
use function array_column;
use function array_keys;
use function array_map;
use function count;
use function dirname;
use function preg_match as native_preg_match;
use function Safe\file_get_contents;
use function Safe\fileperms;
use function sprintf;
use function str_replace;
use function strlen;
use function usort;
use const DIRECTORY_SEPARATOR;

/**
 * @private
 */
final class ConsoleScoper
{
    private const VENDOR_DIR_PATTERN = '~((?:.*)\\'.DIRECTORY_SEPARATOR.'vendor)\\'.DIRECTORY_SEPARATOR.'.*~';

    public function __construct(
        private readonly Filesystem $fileSystem,
        private readonly Application $application,
        private readonly ScoperFactory $scoperFactory,
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

        [$files, $excludedFilesWithContents] = self::getFiles($config, $outputDir);

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

        foreach ($excludedFilesWithContents as $excludedFileWithContent) {
            $this->dumpFileWithPermissions(...$excludedFileWithContent);
        }

        $vendorDir = self::findVendorDir(
            [
                ...array_column($files, 2),
                ...array_column($excludedFilesWithContents, 2),
            ],
        );
        $originalVendorDir = self::findVendorDir(
            [
                ...array_column($files, 0),
                ...array_column($excludedFilesWithContents, 0),
            ],
        );

        if (null !== $vendorDir && null !== $originalVendorDir) {
            $originalRootDir = dirname($originalVendorDir);

            $fileHashGenerator = new FileHashGenerator(
                $originalVendorDir,
                $originalRootDir,
                array_column($config->getExcludedFilesWithContents(), 0),
            );
            $fileHashes = $fileHashGenerator->generateHashes();

            $autoload = (new ScoperAutoloadGenerator($symbolsRegistry, $fileHashes))->dump();

            $this->fileSystem->dumpFile(
                $vendorDir.DIRECTORY_SEPARATOR.'scoper-autoload.php',
                $autoload,
            );
        }
    }

    private function dumpFileWithPermissions(
        string $inputFilePath,
        string $inputContents,
        string $outputFilePath
    ): void {
        $this->fileSystem->dumpFile($outputFilePath, $inputContents);

        $originalFilePermissions = fileperms($inputFilePath) & 0o777;

        if ($originalFilePermissions !== 420) {
            // Only change the permissions if necessary
            $this->fileSystem->chmod($outputFilePath, $originalFilePermissions);
        }
    }

    /**
     * @return array{array<array{string, string, string}>, array<array{string, string, string}>}
     */
    private static function getFiles(Configuration $config, string $outputDir): array
    {
        $filesWithContent = $config->getFilesWithContents();
        $excludedFilesWithContents = $config->getExcludedFilesWithContents();

        $commonDirectoryPath = Path::getLongestCommonBasePath(
            ...array_map(
                static fn (string $path) => Path::getDirectory($path),
                array_keys($filesWithContent),
            ),
            ...array_map(
                static fn (string $path) => Path::getDirectory($path),
                array_keys($excludedFilesWithContents),
            ),
        );
        Assert::notNull($commonDirectoryPath);

        $mapFiles = static fn (array $inputFileTuple) => [
            Path::normalize($inputFileTuple[0]),
            $inputFileTuple[1],
            $outputDir.str_replace($commonDirectoryPath, '', Path::normalize($inputFileTuple[0])),
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
            static fn ($a, $b) => strlen((string) $a) <=> strlen((string) $b),
        );

        return (0 === count($vendorDirs)) ? null : (string) $vendorDirs[0];
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
                    $inputFilePath,
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

        $this->dumpFileWithPermissions(
            $inputFilePath,
            $scoppedContent,
            $outputFilePath,
        );

        if (!isset($exception)) {
            $logger->outputSuccess($inputFilePath);
        }
    }
}
