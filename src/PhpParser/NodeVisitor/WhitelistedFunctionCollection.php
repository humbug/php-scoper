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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use PhpParser\Node\Name\FullyQualified;
use function count;

final class WhitelistedFunctionCollection implements IteratorAggregate, Countable
{
    /**
     * @var FullyQualified[][]
     */
    private $nodes = [];

    public function add(FullyQualified $original, FullyQualified $alias): void
    {
        $this->nodes[] = [$original, $alias];
    }

    /**
     * @inheritdoc
     */
    public function count(): int
    {
        return count($this->nodes);
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->nodes);
    }
}
