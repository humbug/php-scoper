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

namespace Humbug\PhpScoper;

use Closure;
use InvalidArgumentException;
use Iterator;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @final
 * TODO: make this class as final as soon as the underlying deprecated class is removed.
 */
class Configuration
{
    private const FINDER_KEYWORD = 'finders';
    private const PATCHERS_KEYWORD = 'patchers';
    private const WHITELIST_KEYWORD = 'whitelist';
    private const GLOBAL_NAMESPACE_KEYWORD = 'global_namespace_whitelist';

    private const KEYWORDS = [
        self::FINDER_KEYWORD,
        self::PATCHERS_KEYWORD,
        self::WHITELIST_KEYWORD,
        self::GLOBAL_NAMESPACE_KEYWORD,
    ];

    private $path;
    private $filesWithContents;
    private $patchers;
    private $whitelist;

    /**
     * @param string|null $path  Absolute path to the configuration file.
     * @param string[]    $paths List of paths to append besides the one configured
     *
     * @return self
     */
    public static function load(string $path = null, array $paths = []): self
    {
        if (null === $path) {
            $config = [];
        } else {
            $config = include $path;

            if (false === is_array($config)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Expected configuration to be an array, found "%s" instead.',
                        gettype($config)
                    )
                );
            }
        }

        self::validateConfigKeys($config);

        $patchers = self::retrievePatchers($config);
        $whitelist = self::retrieveWhitelist($config);

        $finders = self::retrieveFinders($config);
        $filesFromPaths = self::retrieveFilesFromPaths($paths);
        $filesWithContents = self::retrieveFilesWithContents(chain($filesFromPaths, ...$finders));

        return new self($path, $filesWithContents, $patchers, $whitelist);
    }

    /**
     * @param string|null        $path                        Absolute path to the configuration file loaded.
     * @param [string, string][] $filesWithContents           Array of tuple with the first argument being the file path and the second its contents
     * @param callable[]         $patchers                    List of closures which can alter the content of the files being
     *                                                        scoped.
     * @param string[]           $whitelist                   List of classes that will not be scoped.
     * @param Closure            $globalNamespaceWhitelisters Closure taking a class name from the global namespace as an argument and
     *                                                        returning a boolean which if `true` means the class should be scoped
     *                                                        (i.e. is ignored) or scoped otherwise.
     */
    private function __construct(
        ?string $path,
        array $filesWithContents,
        array $patchers,
        array $whitelist
    ) {
        $this->path = $path;
        $this->filesWithContents = $filesWithContents;
        $this->patchers = $patchers;
        $this->whitelist = $whitelist;
    }

    public function withPaths(array $paths): self
    {
        $filesWithContents = self::retrieveFilesWithContents(
            chain(
                self::retrieveFilesFromPaths(
                    array_unique($paths)
                )
            )
        );

        return new self(
            $this->path,
            array_merge($this->filesWithContents, $filesWithContents),
            $this->patchers,
            $this->whitelist
        );
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFilesWithContents(): array
    {
        return $this->filesWithContents;
    }

    /**
     * @return callable[]
     */
    public function getPatchers(): array
    {
        return $this->patchers;
    }

    public function getWhitelist(): array
    {
        return $this->whitelist;
    }

    private static function validateConfigKeys(array $config): void
    {
        array_map(
            ['self', 'validateConfigKey'],
            array_keys($config)
        );
    }

    private static function validateConfigKey(string $key): void
    {
        if (false === in_array($key, self::KEYWORDS)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid configuration key value "%s" found.',
                    $key
                )
            );
        }
    }

    private static function retrievePatchers(array $config): array
    {
        if (false === array_key_exists(self::PATCHERS_KEYWORD, $config)) {
            return [];
        }

        $patchers = $config[self::PATCHERS_KEYWORD];

        if (false === is_array($patchers)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected patchers to be an array of callables, found "%s" instead.',
                    gettype($patchers)
                )
            );
        }

        foreach ($patchers as $index => $patcher) {
            if (is_callable($patcher)) {
                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Expected patchers to be an array of callables, the "%d" element is not.',
                    $index
                )
            );
        }

        return $patchers;
    }

    private static function retrieveWhitelist(array $config): array
    {
        if (false === array_key_exists(self::WHITELIST_KEYWORD, $config)) {
            return [];
        }

        $whitelist = $config[self::WHITELIST_KEYWORD];

        if (false === is_array($whitelist)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected whitelist to be an array of strings, found "%s" instead.',
                    gettype($whitelist)
                )
            );
        }

        foreach ($whitelist as $index => $className) {
            if (is_string($className)) {
                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    'Expected whitelist to be an array of string, the "%d" element is not.',
                    $index
                )
            );
        }

        return $whitelist;
    }

    private static function retrieveFinders(array $config): array
    {
        if (false === array_key_exists(self::FINDER_KEYWORD, $config)) {
            return [];
        }

        $finders = $config[self::FINDER_KEYWORD];

        if (false === is_array($finders)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected finders to be an array of "%s", found "%s" instead.',
                    Finder::class,
                    gettype($finders)
                )
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
                    $index
                )
            );
        }

        return $finders;
    }

    /**
     * @param string[] $paths
     *
     * @return iterable
     */
    private static function retrieveFilesFromPaths(array $paths): iterable
    {
        if ([] === $paths) {
            return [];
        }

        $pathsToSearch = [];
        $filesToAppend = [];

        foreach ($paths as $path) {
            if (false === file_exists($path)) {
                throw new RuntimeException(
                    sprintf(
                        'Could not find the file "%s".',
                        $path
                    )
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
            ->sortByName()
        ;

        return $finder;
    }

    /**
     * @param Iterator $files
     *
     * @return [string, string][] Array of tuple with the first argument being the file path and the second its contents
     */
    private static function retrieveFilesWithContents(Iterator $files): array
    {
        return array_reduce(
            iterator_to_array($files),
            function (array $files, SplFileInfo $fileInfo): array {
                $file = $fileInfo->getRealPath();

                if (false === $file) {
                    throw new RuntimeException(
                        sprintf(
                            'Could not find the file "%s".',
                            (string) $fileInfo
                        )
                    );
                }

                if (false === is_readable($file)) {
                    throw new RuntimeException(
                        sprintf(
                            'Could not read the file "%s".',
                            $file
                        )
                    );
                }

                $files[$fileInfo->getRealPath()] = [$fileInfo->getRealPath(), file_get_contents($file)];

                return $files;
            },
            []
        );
    }
}
