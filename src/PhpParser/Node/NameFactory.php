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
use Stringable;

final class NameFactory
{
    use NotInstantiable;

    /**
     * @param string|Name|string[]|null $name1
     * @param string|Name|string[]|null $name2
     */
    public static function concat(
        array|Name|string|null $name1,
        array|Name|string|null $name2,
        ?array $attributes = null,
    ): Name {
        if (null === $name1 && null === $name2) {
            throw new InvalidArgumentException('Expected one of the names to not be null');
        }

        $newAttributes = self::getConcatenatedNamesAttributes($name1, $name2, $attributes);

        return Name::concat($name1, $name2, $newAttributes);
    }

    /**
     * @param string|Stringable|string[]|Name|null $name1
     * @param string|Stringable|string[]|Name|null $name2
     */
    public static function getConcatenatedNamesAttributes(
        string|Stringable|array|Name|null $name1,
        string|Stringable|array|Name|null $name2,
        ?array $attributes = null,
    ): array {
        return match (true) {
            $name2 instanceof Name => $attributes ?? $name2->getAttributes(),
            $name1 instanceof Name => $attributes ?? $name1->getAttributes(),
            default => $attributes ?? [],
        };
    }
}
