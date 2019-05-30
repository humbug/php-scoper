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

use Humbug\PhpScoper\Console\Application;
use Humbug\PhpScoper\Console\ApplicationFactory;
use Iterator;
use PackageVersions\Versions;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use function array_pop;
use function count;
use function is_object;
use function is_string;
use function method_exists;
use function str_split;
use function strrpos;
use function substr;

function create_application(): Application
{
    return (new ApplicationFactory())->create();
}

function get_php_scoper_version(): string
{
    $rawVersion = Versions::getVersion('humbug/php-scoper');

    [$prettyVersion, $commitHash] = explode('@', $rawVersion);

    return $prettyVersion.'@'.substr($commitHash, 0, 7);
}

/**
 * @param string[] $paths Absolute paths
 *
 * @return string
 */
function get_common_path(array $paths): string
{
    $nbPaths = count($paths);
    if (0 === $nbPaths) {
        return '';
    }
    $pathRef = array_pop($paths);
    if (1 === $nbPaths) {
        $commonPath = $pathRef;
    } else {
        $commonPath = '';
        foreach (str_split($pathRef) as $pos => $char) {
            foreach ($paths as $path) {
                if (!isset($path[$pos]) || $path[$pos] !== $char) {
                    break 2;
                }
            }
            $commonPath .= $char;
        }
    }
    foreach (['/', '\\'] as $separator) {
        $lastSeparatorPos = strrpos($commonPath, $separator);
        if (false !== $lastSeparatorPos) {
            $commonPath = rtrim(substr($commonPath, 0, $lastSeparatorPos), $separator);

            break;
        }
    }

    return $commonPath;
}

function chain(iterable ...$iterables): Iterator
{
    foreach ($iterables as $iterable) {
        foreach ($iterable as $key => $value) {
            yield $key => $value;
        }
    }
}

function is_stringable($value): bool
{
    return
        null === $value
        || is_string($value)
        || $value instanceof Name
        || $value instanceof Identifier
        || (is_object($value) && method_exists($value, '__toString'))
    ;
}
