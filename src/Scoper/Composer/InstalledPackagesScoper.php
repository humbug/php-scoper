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

use Humbug\PhpScoper\Scoper;
use LogicException;

final class InstalledPackagesScoper implements Scoper
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
    public function scope(string $filePath, string $prefix, array $patchers, callable $globalWhitelister): string
    {
        if (null === self::$filePattern) {
            throw new LogicException('Cannot be used without being initialised first.');
        }

        if (1 !== preg_match(self::$filePattern, $filePath)) {
            return $this->decoratedScoper->scope($filePath, $prefix, $patchers, $globalWhitelister);
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

    private function prefixLockPackages(array $packages, string $prefix): array
    {
        foreach ($packages as $index => $package) {
            $packages[$index] = AutoloadPrefixer::prefixPackageAutoloads($package, $prefix);
        }

        return $packages;
    }
}
