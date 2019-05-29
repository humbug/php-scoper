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
        // BetterReflection stub: leave the stub map unchanged
        //
        static function (string $filePath, string $prefix, string $contents): string {
            if ('vendor/roave/better-reflection/src/SourceLocator/SourceStubber/PhpStormStubsMap.php' === $filePath) {
                $contents = str_replace(
                    [
                        $prefix.'\\\\',
                        $prefix.'\\',
                        'namespace Roave\BetterReflection\SourceLocator\SourceStubber;',
                    ],
                    [
                        '',
                        '',
                        sprintf(
                            'namespace %s\Roave\BetterReflection\SourceLocator\SourceStubber;',
                            $prefix
                        ),
                    ],
                    $contents
                );
            }

            return $contents;
        },
    ],
];
