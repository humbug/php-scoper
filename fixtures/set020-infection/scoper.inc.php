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
            if ($filePath === realpath(__DIR__.'/vendor/infection/infection/app/bootstrap.php')) {
                return str_replace($prefix.'\INFECTION_COMPOSER_INSTALL;', 'INFECTION_COMPOSER_INSTALL;', $contents);
            }
        },
    ],
];
