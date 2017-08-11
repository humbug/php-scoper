<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor;

use ArrayIterator;
use function Humbug\PhpScoper\deep_clone;
use IteratorAggregate;
use PhpParser\Node\Stmt\Use_;
use Traversable;

final class UseStmtCollection implements IteratorAggregate
{
    /**
     * @var Use_[]
     */
    private $nodes = [];

    public function add(Use_ $node)
    {
        $this->nodes[] = deep_clone($node);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->nodes);
    }
}