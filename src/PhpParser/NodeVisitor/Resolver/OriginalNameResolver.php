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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver;

use Humbug\PhpScoper\NotInstantiable;
use PhpParser\Node\Name;

final class OriginalNameResolver
{
    use NotInstantiable;

    private const ORIGINAL_NAME_ATTRIBUTE = 'originalName';

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
