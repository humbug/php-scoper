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

return [
    'patchers' => [
        function (string $filePath, string $prefix, string $contents): string {
            //
            // Infection shared global constant patch
            // @see https://github.com/humbug/php-scoper/issues/171
            //
            if ($filePath === realpath(__DIR__.'/vendor/infection/infection/app/bootstrap.php')) {
                return str_replace($prefix.'\INFECTION_COMPOSER_INSTALL;', 'INFECTION_COMPOSER_INSTALL;', $contents);
            }

            return $contents;
        },
        function (string $filePath, string $prefix, string $contents): string {
            //
            // Infection IncludeInterceptor patch
            //
            if ($filePath === realpath(__DIR__.'/vendor/infection/infection/src/TestFramework/Config/MutationConfigBuilder.php')) {
                return str_replace(
                    'use Infection\\\\StreamWrapper\\\\IncludeInterceptor;',
                    'use '.$prefix.'\\\\Infection\\\\StreamWrapper\\\\IncludeInterceptor;',
                    $contents
                );
            }

            return $contents;
        },
        function (string $filePath, string $prefix, string $contents): string {
            //
            // PHP-Parser patch
            //
            if ($filePath === realpath(__DIR__.'/vendor/nikic/php-parser/lib/PhpParser/NodeAbstract.php')) {
                $length = 15 + strlen($prefix) + 1;

                return preg_replace(
                    '%strpos\((.+?)\) \+ 15%',
                    sprintf('strpos($1) + %d', $length),
                    $contents
                );
            }

            return $contents;
        },
    ],
];
