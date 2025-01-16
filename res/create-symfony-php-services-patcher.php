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

/**
 * Creates a patcher able to fix the paths of the Symfony PHP configuration files.
 *
 * @param string|array<string> $filesPath
 */
return static function (array|string $fileOrFilesPath): Closure {
    $filesPath = (array) $fileOrFilesPath;

    return static function (string $filePath, string $prefix, string $contents) use ($filesPath): string {
        if (!in_array($filePath, $filesPath, true)) {
            return $contents;
        }

        return preg_replace(
            '/(.*->load\((?:\n\s+)?\')(.+?\\\\)(\',.*)/',
            '$1'.$prefix.'\\\\$2$3',
            $contents,
        );
    };
};
