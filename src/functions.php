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

namespace Humbug\PhpScoper;

use Composer\InstalledVersions;
use Iterator;
use function str_starts_with;
use function substr;

function get_php_scoper_version(): string
{
    // Since PHP-Scoper relies on COMPOSER_ROOT_VERSION the version parsed by PackageVersions, we rely on Box
    // placeholders in order to get the right version for the PHAR.
    if (str_starts_with(__FILE__, 'phar:')) {
        return '@git_version_placeholder@';
    }

    $prettyVersion = InstalledVersions::getPrettyVersion('humbug/php-scoper');
    $commitHash = InstalledVersions::getReference('humbug/php-scoper');
    $shortCommitHash = null === $commitHash ? 'local' : substr($commitHash, 0, 7);

    return $prettyVersion.'@'.$shortCommitHash;
}

/**
 * @template T
 *
 * @param iterable<T> ...$iterables
 * @return Iterator<T>
 */
function chain(iterable ...$iterables): Iterator
{
    foreach ($iterables as $iterable) {
        yield from $iterable;
    }
}
