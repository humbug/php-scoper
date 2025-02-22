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

/**
 * @phpstan-import-type Attributes from NameFactory
 */
final class FullyQualifiedFactory
{
    use NotInstantiable;

    /**
     * @param string|Name|string[]|null $name1
     * @param string|Name|string[]|null $name2
     * @param Attributes|null           $attributes
     */
    public static function concat(
        array|Name|string|null $name1,
        array|Name|string|null $name2,
        ?array $attributes = null,
    ): FullyQualified {
        if (null === $name1 && null === $name2) {
            throw new InvalidArgumentException('Expected one of the names to not be null');
        }

        $newAttributes = NameFactory::getConcatenatedNamesAttributes($name1, $name2, $attributes);

        return FullyQualified::concat($name1, $name2, $newAttributes);
    }
}
