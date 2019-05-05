<?php

declare(strict_types=1);

require_once 'root-version.php';

$composerRootVersion = get_composer_root_version(get_last_tag_name());

preg_match(
    '/COMPOSER_ROOT_VERSION=\'(?<version>.*?)\'/',
    file_get_contents('.composer-root-version'),
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
