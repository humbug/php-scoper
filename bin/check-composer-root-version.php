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
        // This is the GitHub API playing tricks on us... I could not find a way to reliably fix it so it is just better
        // to avoid bailing out because of it for now.
        return;
    }

    throw $exception;
}

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
