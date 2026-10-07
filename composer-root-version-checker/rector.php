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
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/bin',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withAutoloadPaths([
        __DIR__.'/vendor/autoload.php',
        __DIR__.'/../vendor-bin/rector/vendor/autoload.php',
    ])
    ->withImportNames()
    ->withPhpSets(php82: true)
    ->withComposerBased(phpunit: true);
