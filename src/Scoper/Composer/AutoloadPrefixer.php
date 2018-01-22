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

namespace Humbug\PhpScoper\Scoper\Composer;

/**
 * @private
 */
final class AutoloadPrefixer
{
    /**
     * @param array  $content Decoded JSON
     * @param string $prefix
     *
     * @return array Prefixed decoded JSON
     */
    public static function prefixPackageAutoloads(array $content, string $prefix): array
    {
        if (isset($content['autoload'])) {
            $content['autoload'] = self::prefixAutoloads($content['autoload'], $prefix);
        }

        if (isset($content['autoload-dev'])) {
            $content['autoload-dev'] = self::prefixAutoloads($content['autoload-dev'], $prefix);
        }

        return $content;
    }

    private static function prefixAutoloads(array $autoload, string $prefix): array
    {
        $autoload['psr-4'] = isset($autoload['psr-4']) ? $autoload['psr-4'] : [];

        if (isset($autoload['psr-0'])) {
            $autoload['psr-4'] = self::mergePSRZeroAndFour($autoload['psr-0'], $autoload['psr-4']);
        }
        unset($autoload['psr-0']);

        if (isset($autoload['psr-4'])) {
            $autoload['psr-4'] = self::prefixAutoload($autoload['psr-4'], $prefix);
        }

        return $autoload;
    }

    private static function prefixAutoload(array $autoload, string $prefix): array
    {
        $loader = [];

        foreach ($autoload as $namespace => $paths) {
            $loader[sprintf('%s\\%s', $prefix, $namespace)] = $paths;
        }

        return $loader;
    }

    private static function mergePSRZeroAndFour(array $psrZero, array $psrFour): array
    {
        foreach ($psrZero as $namespace => $path) {
            //Append backslashes, if needed, since psr-0 does not require this
            if ('\\' !== substr($namespace, -1)) {
                $namespace .= '\\';
            }

            $path = self::updatePSRZeroPath($path, $namespace);

            if (!isset($psrFour[$namespace])) {
                $psrFour[$namespace] = $path;

                continue;
            }
            $psrFour[$namespace] = self::mergeNamespaces($namespace, $path, $psrFour);
        }

        return $psrFour;
    }

    private static function updatePSRZeroPath($path, $namespace)
    {
        $namespaceForPsr = str_replace('\\', '/', $namespace);

        if (!is_array($path)) {
            if ('/' !== substr($path, -1)) {
                $path .= '/';
            }

            $path .= $namespaceForPsr.'/';

            return $path;
        }
        foreach ($path as $key => $item) {
            if ('/' !== substr($item, -1)) {
                $item .= '/';
            }

            $item .= $namespaceForPsr.'/';
            $path[$key] = $item;
        }

        return $path;
    }

    /**
     * Deals with the 4 possible scenarios:
     *       PSR0 | PSR4
     * array      |
     * string     |
     * or simply the namepace not existing as a psr-4 entry.
     *
     * @param string$psrZeroNamespace
     * @param string|array $psrZeroPath
     * @param string|array $psrFour
     *
     * @return string|array
     */
    private static function mergeNamespaces(string $psrZeroNamespace, $psrZeroPath, $psrFour)
    {
        // Both strings
        if (is_string($psrFour[$psrZeroNamespace]) && is_string($psrZeroPath)) {
            return [$psrFour[$psrZeroNamespace], $psrZeroPath];
        }
        //psr-4 is string, and psr-0 is array
        if (is_string($psrFour[$psrZeroNamespace]) && is_array($psrZeroPath)) {
            $psrZeroPath[] = $psrFour[$psrZeroNamespace];

            return $psrZeroPath;
        }

        //psr-4 is array and psr-0 is string
        if (is_array($psrFour[$psrZeroNamespace]) && is_string($psrZeroPath)) {
            $psrFour[$psrZeroNamespace][] = $psrZeroPath;

            return $psrFour[$psrZeroNamespace];
        }

        if (is_array($psrFour[$psrZeroNamespace]) && is_array($psrZeroPath)) {
            return array_merge($psrFour[$psrZeroNamespace], $psrZeroPath);
        }

        return $psrZeroPath;
    }
}
