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

use Humbug\PhpScoper\Patcher\ComposerPatcher;
use Humbug\PhpScoper\Patcher\Patcher;
use Humbug\PhpScoper\Patcher\PatcherChain;
use Humbug\PhpScoper\Patcher\SymfonyPatcher;
use InvalidArgumentException;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use function array_filter;
use function array_key_exists;
use function array_keys;
use function array_map;
use function array_merge;
use function array_unique;
use function array_unshift;
use function bin2hex;
use function dirname;
use function file_exists;
use function gettype;
use function Humbug\PhpScoper\chain;
use function in_array;
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
use function sprintf;
use function trim;
use const DIRECTORY_SEPARATOR;
use const SORT_STRING;

final class ConfigurationFactory
{
    public const DEFAULT_FILE_NAME = 'scoper.inc.php';

    private Filesystem $fileSystem;
    private SymbolsConfigurationFactory $configurationWhitelistFactory;

    public function __construct(
        Filesystem $fileSystem,
        SymbolsConfigurationFactory $configurationWhitelistFactory
    ) {
        $this->fileSystem = $fileSystem;
        $this->configurationWhitelistFactory = $configurationWhitelistFactory;
    }

    /**
     * @param non-empty-string|null  $path  Absolute canonical path to the configuration file.
     * @param list<non-empty-string> $paths List of absolute canonical paths to append besides the one configured
     */
    public function create(?string $path = null, array $paths = []): Configuration
    {
        if (null === $path) {
            $config = [];
        } else {
            $config = $this->loadConfigFile($path);
        }

        self::validateConfigKeys($config);

        $prefix = self::retrievePrefix($config);

        $excludedFiles = null === $path
            ? []
            : $this->retrieveExcludedFiles(
                dirname($path),
                $config,
            );

        $patchers = self::retrievePatchers($config);

        array_unshift($patchers, new SymfonyPatcher());
        array_unshift($patchers, new ComposerPatcher());

        $symbolsConfiguration = $this->configurationWhitelistFactory->createSymbolsConfiguration($config);

        $finders = self::retrieveFinders($config);
        $filesFromPaths = self::retrieveFilesFromPaths($paths);
        $filesWithContents = self::retrieveFilesWithContents(chain($filesFromPaths, ...$finders));

        return new Configuration(
            $path,
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
                    array_unique($paths, SORT_STRING),
                ),
            ),
        );

        return new Configuration(
            $config->getPath(),
            $config->getPrefix(),
            array_merge(
                $config->getFilesWithContents(),
                $filesWithContents,
            ),
            $config->getExcludedFilesWithContents(),
            $config->getPatcher(),
            $config->getSymbolsConfiguration(),
        );
    }

    public function createWithPrefix(Configuration $config, string $prefix): Configuration
    {
        $prefix = self::retrievePrefix([ConfigurationKeys::PREFIX_KEYWORD => $prefix]);

        return new Configuration(
            $config->getPath(),
            $prefix,
            $config->getFilesWithContents(),
            $config->getExcludedFilesWithContents(),
            $config->getPatcher(),
            $config->getSymbolsConfiguration(),
        );
    }

    private function loadConfigFile(string $path): array
    {
        if (!$this->fileSystem->isAbsolutePath($path)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected the path of the configuration file to load to be an absolute path, got "%s" instead',
                    $path,
                ),
            );
        }

        if (!file_exists($path)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected the path of the configuration file to exists but the file "%s" could not be found',
                    $path,
                ),
            );
        }

        $isADirectoryLink = is_link($path)
            && false !== native_readlink($path)
            && is_file(native_readlink($path));

        if (!$isADirectoryLink && !is_file($path)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected the path of the configuration file to be a file but "%s" appears to be a directory.',
                    $path,
                ),
            );
        }

        $config = include $path;

        if (!is_array($config)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected configuration to be an array, found "%s" instead.',
                    gettype($config),
                ),
            );
        }

        return $config;
    }

    private static function validateConfigKeys(array $config): void
    {
        array_map(
            static fn (string $key) => self::validateConfigKey($key),
            array_keys($config),
        );
    }

    private static function validateConfigKey(string $key): void
    {
        if (in_array($key, ConfigurationKeys::KEYWORDS, true)) {
            return;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Invalid configuration key value "%s" found.',
                $key,
            ),
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
     * @return array<(callable(string,string,string): string)|Patcher>
     */
    private static function retrievePatchers(array $config): array
    {
        if (!array_key_exists(ConfigurationKeys::PATCHERS_KEYWORD, $config)) {
            return [];
        }

        $patchers = $config[ConfigurationKeys::PATCHERS_KEYWORD];

        if (!is_array($patchers)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected patchers to be an array of callables, found "%s" instead.',
                    gettype($patchers),
                ),
            );
        }

        foreach ($patchers as $index => $patcher) {
            if (is_callable($patcher)) {
                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Expected patchers to be an array of callables, the "%d" element is not.',
                    $index,
                ),
            );
        }

        return $patchers;
    }

    /**
     * @return string[] Absolute paths
     */
    private function retrieveExcludedFiles(string $dirPath, array $config): array
    {
        if (!array_key_exists(ConfigurationKeys::EXCLUDED_FILES_KEYWORD, $config)) {
            return [];
        }

        $excludedFiles = $config[ConfigurationKeys::EXCLUDED_FILES_KEYWORD];

        if (!is_array($excludedFiles)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected excluded files to be an array of strings, found "%s" instead.',
                    gettype($excludedFiles),
                ),
            );
        }

        foreach ($excludedFiles as $index => $file) {
            if (!is_string($file)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Expected excluded files to be an array of string, the "%d" element is not.',
                        $index,
                    ),
                );
            }

            if (!$this->fileSystem->isAbsolutePath($file)) {
                $file = $dirPath.DIRECTORY_SEPARATOR.$file;
            }

            $excludedFiles[$index] = realpath($file);
        }

        return array_filter($excludedFiles);
    }

    /**
     * @return Finder[]
     */
    private static function retrieveFinders(array $config): array
    {
        if (!array_key_exists(ConfigurationKeys::FINDER_KEYWORD, $config)) {
            return [];
        }

        $finders = $config[ConfigurationKeys::FINDER_KEYWORD];

        if (!is_array($finders)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected finders to be an array of "%s", found "%s" instead.',
                    Finder::class,
                    gettype($finders),
                ),
            );
        }

        foreach ($finders as $index => $finder) {
            if ($finder instanceof Finder) {
                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Expected finders to be an array of "%s", the "%d" element is not.',
                    Finder::class,
                    $index,
                ),
            );
        }

        return $finders;
    }

    /**
     * @param string[] $paths
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
                throw new RuntimeException(
                    sprintf(
                        'Could not find the file "%s".',
                        $path,
                    ),
                );
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
                static fn (SplFileInfo $fileInfo) => $fileInfo->isLink() ? false : null,
            )
            ->sortByName();

        return $finder;
    }

    /**
     * @param iterable<SplFileInfo|string> $files
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
                throw new RuntimeException(
                    sprintf(
                        'Could not find the file "%s".',
                        (string) $filePathOrFileInfo,
                    ),
                );
            }

            if (!is_readable($filePath)) {
                throw new RuntimeException(
                    sprintf(
                        'Could not read the file "%s".',
                        $filePath,
                    ),
                );
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
