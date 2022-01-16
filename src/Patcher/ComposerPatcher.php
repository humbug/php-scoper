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

namespace Humbug\PhpScoper\Patcher;

use function Safe\preg_replace;
use function Safe\sprintf;
use function str_replace;
use function strpos;

final class ComposerPatcher
{
    private const PATHS = [
        'src/Composer/Package/Loader/ArrayLoader.php',
        'src/Composer/Package/Loader/RootPackageLoader.php',
    ];

    public function __invoke(string $filePath, string $prefix, string $contents): string
    {
        if (!self::isSupportedFile($filePath)) {
            return $contents;
        }

        return str_replace(
            [
                '\'Composer\\Package\\RootPackage\'',
                '\'Composer\\\\Package\\\\RootPackage\'',
                ' Composer\\Package\\RootPackage ',

                '\'Composer\\Package\\CompletePackage\'',
                '\'Composer\\\\Package\\\\CompletePackage\'',
                ' Composer\\Package\\CompletePackage ',
            ],
            [
                '\''.$prefix.'\\Composer\\Package\\RootPackage\'',
                '\''.$prefix.'\\\\Composer\\\\Package\\\\RootPackage\'',
                ' '.$prefix.'\\Composer\\Package\\RootPackage ',

                '\''.$prefix.'\\Composer\\Package\\CompletePackage\'',
                '\''.$prefix.'\\\\Composer\\\\Package\\\\CompletePackage\'',
                ' '.$prefix.'\\Composer\\Package\\CompletePackage ',
            ],
            $contents,
        );
    }

    private static function isSupportedFile(string $filePath): bool
    {
        foreach (self::PATHS as $path) {
            if (false !== strpos($filePath, $path)) {
                return true;
            }
        }

        return false;
    }
}
