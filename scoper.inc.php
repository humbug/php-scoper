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

$jetBrainStubs = (static function (): array {
    $packageDir = __DIR__.'/vendor/jetbrains/phpstorm-stubs';
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
})();

var_dump(array_map(static fn ($path) => substr($path, 72), $jetBrainStubs));
exit;

return [
    'expose-global-functions' => true,
    'expose-global-classes' => true,
    'exclude-classes' => [
        'Isolated\Symfony\Component\Finder\Finder',
    ],
    'exclude-functions' => [
        'trigger_deprecation',
    ],
    'exclude-constants' => [
        // Symfony global constants
        '/^SYMFONY\_[\p{L}_]+$/',
    ],
    'exclude-files' => $jetBrainStubs,
    'patchers' => [
        //
        // PHPStorm stub map: adjust the namespace to fix the autoloading, but keep it
        // unchanged otherwise.
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
                        $prefix,
                    ),
                ],
                $contents,
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

            $classPosition = mb_strpos($originalContents, 'final class Reflector');
            $prefixedClassPosition = mb_strpos($contents, 'final class Reflector');

            return sprintf(
                '%s%s',
                mb_substr($contents, 0, $prefixedClassPosition),
                mb_substr($originalContents, $classPosition),
            );
        },
    ],
];
