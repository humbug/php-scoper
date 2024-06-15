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

namespace Humbug\PhpScoper\Configuration;

use Humbug\PhpScoper\Configuration\Throwable\InvalidConfigurationFile;
use Humbug\PhpScoper\Configuration\Throwable\InvalidConfigurationValue;
use Humbug\PhpScoper\Configuration\Throwable\UnknownConfigurationKey;
use Humbug\PhpScoper\Patcher\ComposerPatcher;
use Humbug\PhpScoper\Patcher\Patcher;
use Humbug\PhpScoper\Patcher\PatcherChain;
use Humbug\PhpScoper\Patcher\SymfonyParentTraitPatcher;
use Humbug\PhpScoper\Patcher\SymfonyPatcher;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_unique;
use function array_unshift;
use function bin2hex;
use function dirname;
use function file_exists;
use function Humbug\PhpScoper\chain;
use function is_array;
use function is_callable;
use function is_dir;
use function is_file;
use function is_link;
use function is_readable;
use function is_string;
use function random_bytes;
use function readlink as native_readlink;
use function realpath;
use function Safe\file_get_contents;
use function trim;
use const DIRECTORY_SEPARATOR;

final readonly class ConfigurationFactory
{
    public const DEFAULT_FILE_NAME = 'scoper.inc.php';

    public function __construct(
        private Filesystem $fileSystem,
        private SymbolsConfigurationFactory $symbolsConfigurationFactory,
    ) {
    }

    /**
     * @param non-empty-string|null  $path  Absolute canonical path to the configuration file.
     * @param list<non-empty-string> $paths List of absolute canonical paths to append besides the one configured
     *
     * @throws InvalidConfigurationValue
     * @throws UnknownConfigurationKey
     */
    public function create(?string $path = null, array $paths = []): Configuration
    {
        $config = null === $path ? [] : $this->loadConfigFile($path);

        self::validateConfigKeys($config);

        $prefix = self::retrievePrefix($config);
        $outputDir = self::retrieveOutputDir($config);

        $excludedFiles = null === $path
            ? []
            : $this->retrieveExcludedFiles(
                dirname($path),
                $config,
            );

        $patchers = self::retrievePatchers($config);

        array_unshift($patchers, new SymfonyPatcher());
        array_unshift($patchers, new SymfonyParentTraitPatcher());
        array_unshift($patchers, new ComposerPatcher());

        $symbolsConfiguration = $this->symbolsConfigurationFactory->createSymbolsConfiguration($config);

        $finders = self::retrieveFinders($config);
        $filesFromPaths = self::retrieveFilesFromPaths($paths);
        $filesWithContents = self::retrieveFilesWithContents(chain($filesFromPaths, ...$finders));

        return new Configuration(
            $path,
            $outputDir,
            $prefix,
            $filesWithContents,
            self::retrieveFilesWithContents($excludedFiles),
            new PatcherChain($patchers),
            $symbolsConfiguration,
        );
    }

    /**
     * @param string[] $paths
     */
    public function createWithPaths(Configuration $config, array $paths): Configuration
    {
        $filesWithContents = self::retrieveFilesWithContents(
            chain(
                self::retrieveFilesFromPaths(
                    array_unique($paths),
                ),
            ),
        );

        return $config->withFilesWithContents([
            ...$config->getFilesWithContents(),
            ...$filesWithContents,
        ]);
    }

    public function createWithPrefix(Configuration $config, string $prefix): Configuration
    {
        $prefix = self::retrievePrefix([ConfigurationKeys::PREFIX_KEYWORD => $prefix]);

        return $config->withPrefix($prefix);
    }

    /**
     * @throws InvalidConfigurationValue
     */
    private function loadConfigFile(string $path): array
    {
        if (!$this->fileSystem->isAbsolutePath($path)) {
            throw InvalidConfigurationFile::forNonAbsolutePath($path);
        }

        if (!file_exists($path)) {
            throw InvalidConfigurationFile::forFileNotFound($path);
        }

        $isADirectoryLink = is_link($path)
            && false !== native_readlink($path)
            && is_file(native_readlink($path));

        if (!$isADirectoryLink && !is_file($path)) {
            throw InvalidConfigurationFile::forNotAFile($path);
        }

        $config = include $path;

        if (!is_array($config)) {
            throw InvalidConfigurationFile::forInvalidValue($path);
        }

        return $config;
    }

    private static function validateConfigKeys(array $config): void
    {
        array_map(
            ConfigurationKeys::assertIsValidKey(...),
            array_keys($config),
        );
    }

    /**
     * @return non-empty-string
     */
    private static function retrievePrefix(array $config): string
    {
        $prefix = trim((string) ($config[ConfigurationKeys::PREFIX_KEYWORD] ?? ''));

        return '' === $prefix ? self::generateRandomPrefix() : $prefix;
    }

    /**
     * @return non-empty-string|null
     */
    private static function retrieveOutputDir(array $config): ?string
    {
        $outputDir = trim((string) ($config[ConfigurationKeys::OUTPUT_DIR_KEYWORD] ?? ''));

        return '' === $outputDir ? null : $outputDir;
    }

    /**
     * @throws InvalidConfigurationValue
     *
     * @return array<(callable(string,string,string): string)|Patcher>
     */
    private static function retrievePatchers(array $config): array
    {
        if (!array_key_exists(ConfigurationKeys::PATCHERS_KEYWORD, $config)) {
            return [];
        }

        $patchers = $config[ConfigurationKeys::PATCHERS_KEYWORD];

        if (!is_array($patchers)) {
            throw InvalidConfigurationValue::forInvalidPatchersType($patchers);
        }

        foreach ($patchers as $index => $patcher) {
            if (!is_callable($patcher)) {
                throw InvalidConfigurationValue::forInvalidPatcherType($index, $patcher);
            }
        }

        return $patchers;
    }

    /**
     * @throws InvalidConfigurationValue
     *
     * @return string[] Absolute paths
     */
    private function retrieveExcludedFiles(string $dirPath, array $config): array
    {
        if (!array_key_exists(ConfigurationKeys::EXCLUDED_FILES_KEYWORD, $config)) {
            return [];
        }

        $excludedFiles = $config[ConfigurationKeys::EXCLUDED_FILES_KEYWORD];

        if (!is_array($excludedFiles)) {
            throw InvalidConfigurationValue::forInvalidExcludedFilesTypes($excludedFiles);
        }

        foreach ($excludedFiles as $index => $file) {
            if (!is_string($file)) {
                throw InvalidConfigurationValue::forInvalidExcludedFilePath($index, $excludedFiles);
            }

            if (!$this->fileSystem->isAbsolutePath($file)) {
                $file = $dirPath.DIRECTORY_SEPARATOR.$file;
            }

            $excludedFiles[$index] = realpath($file);
        }

        // We ignore files not found excluded file as we do not want to bail out just because a file we do not want to
        // include does not exist.
        return array_filter($excludedFiles);
    }

    /**
     * @throws InvalidConfigurationValue
     *
     * @return Finder[]
     */
    private static function retrieveFinders(array $config): array
    {
        if (!array_key_exists(ConfigurationKeys::FINDER_KEYWORD, $config)) {
            return [];
        }

        $finders = $config[ConfigurationKeys::FINDER_KEYWORD];

        if (!is_array($finders)) {
            throw InvalidConfigurationValue::forInvalidFinderTypes($finders);
        }

        foreach ($finders as $index => $finder) {
            if ($finder instanceof Finder) {
                continue;
            }

            throw InvalidConfigurationValue::forInvalidFinderType($index, $finder);
        }

        return $finders;
    }

    /**
     * @param string[] $paths
     *
     * @throws InvalidConfigurationValue
     *
     * @return iterable<SplFileInfo>
     */
    private static function retrieveFilesFromPaths(array $paths): iterable
    {
        if ([] === $paths) {
            return [];
        }

        $pathsToSearch = [];
        $filesToAppend = [];

        foreach ($paths as $path) {
            if (!file_exists($path)) {
                throw InvalidConfigurationValue::forFileNotFound($path);
            }

            if (is_dir($path)) {
                $pathsToSearch[] = $path;
            } else {
                $filesToAppend[] = $path;
            }
        }

        $finder = new Finder();

        $finder->files()
            ->in($pathsToSearch)
            ->append($filesToAppend)
            ->filter(
                static fn (SplFileInfo $fileInfo) => !$fileInfo->isLink(),
            )
            ->sortByName();

        return $finder;
    }

    /**
     * @param iterable<SplFileInfo|string> $files
     *
     * @throws InvalidConfigurationValue
     *
     * @return array<string, array{string, string}> Array of tuple with the first argument being the file path and the second its contents
     */
    private static function retrieveFilesWithContents(iterable $files): array
    {
        $filesWithContents = [];

        foreach ($files as $filePathOrFileInfo) {
            $filePath = $filePathOrFileInfo instanceof SplFileInfo
                ? $filePathOrFileInfo->getRealPath()
                : realpath($filePathOrFileInfo);

            if (!$filePath) {
                throw InvalidConfigurationValue::forFileNotFound((string) $filePathOrFileInfo);
            }

            if (!is_readable($filePath)) {
                throw InvalidConfigurationValue::forUnreadableFile($filePath);
            }

            $filesWithContents[$filePath] = [$filePath, file_get_contents($filePath)];
        }

        return $filesWithContents;
    }

    /**
     * @return non-empty-string
     */
    private static function generateRandomPrefix(): string
    {
        return '_PhpScoper'.bin2hex(random_bytes(6));
    }
}
