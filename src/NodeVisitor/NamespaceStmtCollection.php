<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor;

use ArrayIterator;
use Countable;
use function Humbug\PhpScoper\deep_clone;
use InvalidArgumentException;
use IteratorAggregate;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use Traversable;

final class NamespaceStmtCollection implements IteratorAggregate, Countable
{
    /**
     * @var Namespace_[]
     */
    private $nodes = [];

    public function add(Namespace_ $node)
    {
        $this->nodes[] = deep_clone($node);
    }

    public function getNamespaceName(): Name
    {
        if (0 === count($this->nodes)) {
            throw new InvalidArgumentException('No name can be given: no namespace found.');
        }

        if (1 < count($this->nodes)) {
            throw new InvalidArgumentException('No name can be given: more than one namespace found.');
        }

        return $this->nodes[0]->name;
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
    public function getIterator()
    {
        return new ArrayIterator($this->nodes);
    }
}