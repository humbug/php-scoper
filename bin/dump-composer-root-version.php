<?php

declare(strict_types=1);

require_once __DIR__.'/root-version.php';

$composerRootVersion = get_composer_root_version(get_last_tag_name());

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
