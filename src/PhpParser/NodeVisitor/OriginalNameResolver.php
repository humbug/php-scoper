<?php

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser\NodeVisitor;

use PhpParser\Node\Name;

// TODO: review those classes
final class OriginalNameResolver
{
    private const ORIGINAL_NAME_ATTRIBUTE = 'originalName';

    private function __construct()
    {
    }

    public static function hasOriginalName(Name $namespace): bool
    {
        return $namespace->hasAttribute(self::ORIGINAL_NAME_ATTRIBUTE);
    }

    public static function getOriginalName(Name $name): Name
    {
        if (!self::hasOriginalName($name)) {
            return $name;
        }

        return $name->getAttribute(self::ORIGINAL_NAME_ATTRIBUTE);
    }
}
