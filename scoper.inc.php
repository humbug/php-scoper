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

$jetBrainStubs = (require __DIR__.'/res/get-scoper-phpstorm-stubs.php')();
$jetBrainStubsPatcher = (require __DIR__.'/res/create-scoper-phpstorm-stubs-map-patcher.php')();

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
        $jetBrainStubsPatcher,
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
