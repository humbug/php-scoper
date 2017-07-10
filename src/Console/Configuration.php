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

use InvalidArgumentException;

final class Configuration
{
    private $path;
    private $patchers;
    private $globalNamespaceWhitelisters;

    /**
     * @param string|null         $path            Absolute path to the configuration file loaded
     * @param callable[]          $patchers        List of closures which can alter the content of the files being
     *                                             scoped
     * @param callable[]|string[] $globalNamespace List of class names from the global namespace that should be scoped
     *                                             or closures filtering if the class should be scoped or not
     */
    private function __construct(string $path = null, array $patchers, array $globalNamespace)
    {
        $this->path = $path;
        $this->patchers = $patchers;
        $this->globalNamespaceWhitelisters = $globalNamespace;
    }

    /**
     * @param string|null $path Absolute path to the configuration file.
     *
     * @return self
     */
    public static function load(?string $path): self
    {
        if (null === $path) {
            return new self(null, [], []);
        }

        $config = include $path;

        if (false === is_array($config)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected configuration to be an array, found "%s" instead.',
                    gettype($config)
                )
            );
        }

        $patchers = self::retrievePatchers($config);
        $globalNamespace = self::retrieveGlobalNamespaceWhitelisters($config);

        return new self($path, $patchers, $globalNamespace);
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @return callable[]
     */
    public function getPatchers()
    {
        return $this->patchers;
    }

    /**
     * @return callable[]|string[]
     */
    public function getGlobalNamespaceWhitelisters()
    {
        return $this->globalNamespaceWhitelisters;
    }

    private static function retrievePatchers(array $config): array
    {
        if (false === array_key_exists('patchers', $config)) {
            return [];
        }

        $patchers = $config['patchers'];

        if (false === is_array($patchers)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected patchers to be an array of callables, found "%s" instead.',
                    gettype($patchers)
                )
            );
        }

        foreach ($patchers as $index => $patcher) {
            if (false === is_callable($patcher)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Expected patchers to be an array of callables, the "%d" element is not.',
                        $index
                    )
                );
            }
        }

        return $patchers;
    }

    private static function retrieveGlobalNamespaceWhitelisters(array $config): array
    {
        if (false === array_key_exists('global_namespace_whitelist', $config)) {
            return [];
        }

        $globalNamespace = $config['global_namespace_whitelist'];

        if (false === is_array($globalNamespace)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected "global_namespace" to be an array, found "%s" instead.',
                    gettype($globalNamespace)
                )
            );
        }

        foreach ($globalNamespace as $index => $className) {
            if (false === is_string($className) && false === is_callable($className)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Expected "global_namespace" to be an array of callables or strings, the "%d" element '
                        .'is not.',
                        $index
                    )
                );
            }
        }

        return $globalNamespace;
    }
}
