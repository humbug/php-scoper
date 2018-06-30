<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser\NodeVisitor\Collection;


use ArrayIterator;
use Countable;
use IteratorAggregate;
use PhpParser\Node\Name\FullyQualified;
use function count;

final class UserGlobalFunctionCollection implements IteratorAggregate, Countable
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