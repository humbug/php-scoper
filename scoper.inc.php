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

use Isolated\Symfony\Component\Finder\Finder;

$jetBrainStubs = (static function (): array {
    $files = [];

    foreach (new DirectoryIterator(__DIR__.'/vendor/jetbrains/phpstorm-stubs') as $directoryInfo) {
        if ($directoryInfo->isDot()) {
            continue;
        }

        if (false === $directoryInfo->isDir()) {
            continue;
        }

        if (in_array($directoryInfo->getBasename(), ['tests', 'meta'], true)) {
            continue;
        }

        foreach (new DirectoryIterator($directoryInfo->getPathName()) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            if (1 !== preg_match('/\.php$/', $fileInfo->getBasename())) {
                continue;
            }

            $files[] = $fileInfo->getPathName();
        }
    }

    return $files;
})();

return [
    'whitelist' => [
        Finder::class,
    ],
    'files-whitelist' => $jetBrainStubs,
    'patchers' => [
        //
        // PHPStorm stub map: leave it unchanged
        //
        static function (string $filePath, string $prefix, string $contents): string {
            if ('vendor/jetbrains/phpstorm-stubs/PhpStormStubsMap.php' !== $filePath) {
                return $contents;
            }

            return str_replace(
                [
                    $prefix.'\\\\',
                    $prefix.'\\',
                    'namespace JetBrains\PHPStormStub;',
                ],
                [
                    '',
                    '',
                    sprintf(
                        'namespace %s\JetBrains\PHPStormStub;',
                        $prefix
                    ),
                ],
                $contents
            );
        },
        //
        // Reflector: leave the registered internal symbols unchanged
        //
        static function (string $filePath, string $prefix, string $contents): string {
            if ('src/Reflector.php' !== $filePath) {
                return $contents;
            }

            $originalContents = file_get_contents(__DIR__.'/src/Reflector.php');

            $classPosition = strpos($originalContents, 'final class Reflector');
            $prefixedClassPosition = strpos($contents, 'final class Reflector');

            return sprintf(
                '%s%s',
                substr($contents, 0, $prefixedClassPosition),
                substr($originalContents, $classPosition)
            );
        },
        static function (string $filePath, string $prefix, string $contents): string {
            if ('bin/php-scoper' !== $filePath) {
                return $contents;
            }

            return str_replace(
                '\\'.$prefix.'\Isolated\Symfony\Component\Finder\Finder::class',
                '\Isolated\Symfony\Component\Finder\Finder::class',
                $contents
            );
        },
    ],
];
