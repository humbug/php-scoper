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

$composerRootVersion = get_composer_root_version(get_last_tag_name());

preg_match(
    '/COMPOSER_ROOT_VERSION=\'(?<version>.*?)\'/',
    file_get_contents(__DIR__.'/../.composer-root-version'),
    $matches
);

$currentRootVersion = $matches['version'];

if ($composerRootVersion !== $currentRootVersion) {
    file_put_contents(
        'php://stderr',
        sprintf(
            'Expected the COMPOSER_ROOT_VERSION to be "%s" but got "%s" instead.'.PHP_EOL,
            $composerRootVersion,
            $currentRootVersion
        )
    );

    exit(1);
}
