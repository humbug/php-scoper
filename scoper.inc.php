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
    $files = [];
    $ignoredDirectories = [
        $packageDir.'/tests',
        $packageDir.'/meta',
    ];

    $collectFiles = static function (RecursiveIteratorIterator $iterator) use (&$collectFiles, &$files, $ignoredDirectories): void {
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
                if (str_starts_with($fileInfo->getPathname(),  $ignoredDirectory)) {
                    continue 2;
                }
            }

            $files[] = $fileInfo->getPathName();
        }
    };

    $collectFiles(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($packageDir)));

    return $files;
})();

return [
    'expose-global-functions' => true,
    'expose-global-classes' => true,
    'exclude-classes' => [
        'Isolated\Symfony\Component\Finder\Finder',
    ],
    'exclude-functions' => [
        // symfony/deprecation-contracts
        'trigger_deprecation',

        // nikic/php-parser
        // https://github.com/nikic/PHP-Parser/issues/957
        'assertArgs',
        'ensureDirExists',
        'execCmd',
        'formatErrorMessage',
        'magicSplit',
        'parseArgs',
        'preprocessGrammar',
        'regex',
        'removeTrailingWhitespace',
        'resolveMacros',
        'resolveNodes',
        'resolveStackAccess',
        'showHelp',
    ],
    'exclude-constants' => [
        // Symfony global constants
        '/^SYMFONY\_[\p{L}_]+$/',
    ],
    'exclude-files' => $jetBrainStubs,
    'patchers' => [
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
