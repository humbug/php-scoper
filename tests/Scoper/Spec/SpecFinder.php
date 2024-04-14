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

namespace Humbug\PhpScoper\Scoper\Spec;

use Humbug\PhpScoper\NotInstantiable;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class SpecFinder
{
    use NotInstantiable;

    public const SPECS_PATH = __DIR__.'/../../../specs';
    public const TMP_SPECS_PATH = __DIR__.'/../../../_specs';

    /**
     * @return array{string, iterable<SplFileInfo>}
     */
    public static function findSpecFiles(): array
    {
        $sourceDir = self::TMP_SPECS_PATH;
        $files = self::findFiles($sourceDir);

        if (0 === count($files)) {
            $sourceDir = self::SPECS_PATH;
            $files = self::findFiles($sourceDir);
        }

        $files->sortByName();

        return [$sourceDir, $files];
    }

    /**
     * @return iterable<SplFileInfo>
     */
    public static function findTmpSpecFiles(): iterable
    {
        return self::findFiles(self::TMP_SPECS_PATH);
    }

    private static function findFiles(string $sourceDir): Finder
    {
        return (new Finder())->files()->in($sourceDir);
    }
}
