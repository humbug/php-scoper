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

// excluded, see scoper-phpstorm-stubs-map-patcher.php for more information.

$defaultSource = __DIR__.'/../vendor/jetbrains/phpstorm-stubs';

return static function (?string $stubsDir = null) use ($defaultSource): array {
    $packageDir = $stubsDir ?? $defaultSource;
    $ignoredDirectories = [
        $packageDir.'/tests',
        $packageDir.'/meta',
    ];
    $files = [];

    $collectFiles = static function (RecursiveIteratorIterator $iterator) use (&$files, $ignoredDirectories): void {
        foreach ($iterator as $fileInfo) {
            /** @var SplFileInfo $fileInfo */
            if (str_starts_with($fileInfo->getFilename(), '.')
                || $fileInfo->isDir()
                || !$fileInfo->isReadable()
                || 'php' !== $fileInfo->getExtension()
                // The map needs to be excluded from "exclude-files" as otherwise its namespace cannot be corrected
                // via a patcher
                || $fileInfo->getFilename() === 'PhpStormStubsMap.php'
            ) {
                continue;
            }

            foreach ($ignoredDirectories as $ignoredDirectory) {
                if (str_starts_with($fileInfo->getPathname(), $ignoredDirectory)) {
                    continue 2;
                }
            }

            $files[] = $fileInfo->getPathName();
        }
    };

    $collectFiles(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($packageDir)));

    return $files;
};
