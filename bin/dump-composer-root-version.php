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

require_once __DIR__.'/root-version.php';

try {
    $composerRootVersion = get_composer_root_version(get_last_tag_name());
} catch (RuntimeException $exception) {
    if (false !== getenv('TRAVIS') && false === getenv('GITHUB_TOKEN')) {
        // Ignore this PR to avoid too many builds to fail untimely or locally due to API rate limits because the last
        // release version could not be retrieved.
        return;
    }

    if (100 === $exception->getCode()) {
        return;
    }

    throw $exception;
}

file_put_contents(
    __DIR__.'/../.composer-root-version',
    sprintf(
        <<<'BASH'
#!/usr/bin/env bash

export COMPOSER_ROOT_VERSION='%s'

BASH
        ,
        $composerRootVersion
    )
);

file_put_contents(
    $scrutinizerPath = __DIR__.'/../.scrutinizer.yml',
    preg_replace(
        '/COMPOSER_ROOT_VERSION: \'.*?\'/',
        sprintf(
            'COMPOSER_ROOT_VERSION: \'%s\'',
            $composerRootVersion
        ),
        file_get_contents($scrutinizerPath)
    )
);
