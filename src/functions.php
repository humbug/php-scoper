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
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Parser;
use function array_map;
use function count;
use function is_array;
use function is_object;
use function is_scalar;
use function is_string;
use function method_exists;
use function serialize;
use function strlen;
use function strpos;
use function substr;
use function unserialize;

function create_application(): Application
{
    return (new ApplicationFactory())->create();
}

/**
 * @private
 *
 * @deprecated Will be removed in future releases.
 */
function create_scoper(): Scoper
{
    return (new class() extends ApplicationFactory {
        public static function createScoper(): Scoper
        {
            return parent::createScoper();
        }
    })::createScoper();
}

/**
 * @param string[] $paths Absolute paths
 *
 * @return string
 */
function get_common_path(array $paths): string
{
    if (0 === count($paths)) {
        return '';
    }

    $lastOffset = 1;
    $common = DIRECTORY_SEPARATOR;

    while (false !== ($index = strpos($paths[0], DIRECTORY_SEPARATOR, $lastOffset))) {
        $dirLen = $index - $lastOffset + 1;
        $dir = substr($paths[0], $lastOffset, $dirLen);

        foreach ($paths as $path) {
            if (substr($path, $lastOffset, $dirLen) !== $dir) {
                if (0 < strlen($common) && DIRECTORY_SEPARATOR === $common[strlen($common) - 1]) {
                    $common = substr($common, 0, -1);
                }

                return $common;
            }
        }

        $common .= $dir;
        $lastOffset = $index + 1;
    }

    $common = substr($common, 0, -1);

    if (0 < strlen($common) && DIRECTORY_SEPARATOR === $common[strlen($common) - 1]) {
        $common = substr($common, 0, -1);
    }

    return $common;
}

/**
 * In-house clone functions. Does a partial clone that should be enough to provide the immutability required in some
 * places for the scoper. It however does not guarantee a deep cloning as would be horribly slow for no good reasons.
 * A better alternative would be to find a way to push immutability upstream in PHP-Parser directly.
 *
 * @param Node $node
 *
 * @return Node
 */
function clone_node(Node $node): Node
{
    $clone = deep_clone($node);

    foreach ($node->getAttributes() as $key => $attribute) {
        $clone->setAttribute($key, $attribute);
    }

    return $clone;
}

/**
 * @param mixed $node
 *
 * @return mixed
 *
 * @internal
 */
function deep_clone($node)
{
    if (is_array($node)) {
        return array_map(__FUNCTION__, $node);
    }

    if (null === $node || is_scalar($node)) {
        return $node;
    }

    return unserialize(serialize($node));
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
