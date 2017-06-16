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

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\Scoper;
use LogicException;

final class ComposerInstalledPackagesScoper implements Scoper
{
    /**
     * @var string
     */
    private static $filePattern;

    private $decoratedScoper;

    public function __construct(Scoper $decoratedScoper)
    {
        if (null === self::$filePattern) {
            self::$filePattern = str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                '~composer/installed\.json~'
            );
        }

        $this->decoratedScoper = $decoratedScoper;
    }

    /**
     * Scopes PHP and JSON files related to Composer.
     *
     * {@inheritdoc}
     */
    public function scope(string $filePath, string $prefix): string
    {
        if (null === self::$filePattern) {
            throw new LogicException('Cannot be used without being initialised first.');
        }

        if (1 !== preg_match(self::$filePattern, $filePath)) {
            return $this->decoratedScoper->scope($filePath, $prefix);
        }

        $decodedJson = json_decode(
            file_get_contents($filePath),
            true
        );

        $decodedJson = $this->prefixLockPackages($decodedJson, $prefix);

        return json_encode(
            $decodedJson,
            JSON_PRETTY_PRINT
        );
    }

    /**
     * @param array  $content Decoded JSON of the `composer.lock` file
     * @param string $prefix
     *
     * @return array Prefixed decoded JSON
     */
    private function scopeComposerLockFile(array $content, string $prefix): array
    {
        if (isset($content['packages'])) {
            $content['packages'] = $this->prefixLockPackages($content['packages'], $prefix);
        }

        if (isset($content['packages-dev'])) {
            $content['packages-dev'] = $this->prefixLockPackages($content['packages-dev'], $prefix);
        }

        return $content;
    }

    private function prefixLockPackages(array $packages, string $prefix): array
    {
        foreach ($packages as $index => $package) {
            $packages[$index] = $this->scopeComposerFile($package, $prefix);
        }

        return $packages;
    }

    /**
     * @param array  $content Decoded JSON of the `composer.json` file
     * @param string $prefix
     *
     * @return array Prefixed decoded JSON
     */
    private function scopeComposerFile(array $content, string $prefix): array
    {
        if (isset($content['autoload'])) {
            $content['autoload'] = $this->prefixAutoloads($content['autoload'], $prefix);
        }

        if (isset($content['autoload-dev'])) {
            $content['autoload-dev'] = $this->prefixAutoloads($content['autoload-dev'], $prefix);
        }

        return $content;
    }

    private function prefixAutoloads(array $autoload, string $prefix): array
    {
        if (isset($autoload['psr-4'])) {
            $autoload['psr-4'] = $this->prefixAutoload($autoload['psr-4'], $prefix);
        }

        return $autoload;
    }

    private function prefixAutoload(array $autoload, string $prefix): array
    {
        $loader = [];

        foreach ($autoload as $namespace => $paths) {
            $loader[sprintf('%s\\%s', $prefix, $namespace)] = $paths;
        }

        return $loader;
    }
}
