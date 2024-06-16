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

namespace Humbug\PhpScoper\PhpParser\Node;

use Humbug\PhpScoper\NotInstantiable;
use InvalidArgumentException;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use Stringable;

final class FullyQualifiedFactory
{
    use NotInstantiable;

    /**
     * @param string|Name|string[]|Stringable|null $name1
     * @param string|Name|string[]|Stringable|null $name2
     */
    public static function concat(
        array|Name|string|Stringable|null $name1,
        array|Name|string|Stringable|null $name2,
        ?array $attributes = null,
    ): FullyQualified {
        if (null === $name1 && null === $name2) {
            throw new InvalidArgumentException('Expected one of the names to not be null');
        }

        $newAttributes = NameFactory::getConcatenatedNamesAttributes($name1, $name2, $attributes);

        $name1 = $name1 instanceof Stringable ? (string) $name1 : $name1;
        $name2 = $name2 instanceof Stringable ? (string) $name2 : $name2;

        return FullyQualified::concat($name1, $name2, $newAttributes);
    }
}
