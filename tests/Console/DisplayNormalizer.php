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

use function Safe\preg_match_all;
use function str_replace;
use function strlen;
use function usort;
use const DIRECTORY_SEPARATOR;

final class DisplayNormalizer
{
    public static function normalize(string $display): string
    {
        return self::normalizeDirectorySeparators(
            self::normalizeProgressBar($display),
        );
    }

    public static function normalizeDirectorySeparators(string $display): string
    {
        if ('\\' === DIRECTORY_SEPARATOR && preg_match_all('/\/path\/to(.*\\\\)+/', $display, $match)) {
            /** @phpstan-ignore offsetAccess.notFound */
            $paths = $match[0];
            usort($paths, static fn ($a, $b) => strlen($b) - strlen($a));
            foreach ($paths as $path) {
                $fixedPath = str_replace('\\', '/', $path);
                $display = str_replace($path, $fixedPath, $display);
            }
        }

        return $display;
    }

    public static function normalizeProgressBar(string $display): string
    {
        if ('\\' === DIRECTORY_SEPARATOR && preg_match_all('/\\[=*>?\\-*\\]/', $display, $match)) {
            /** @phpstan-ignore offsetAccess.notFound */
            $bars = $match[0];
            foreach ($bars as $bar) {
                $fixedBar = str_replace(['>', '-', '='], ['░', '░', '▓'], $bar);
                $display = str_replace($bar, $fixedBar, $display);
            }
        }

        return $display;
    }

    private function __construct()
    {
    }
}
