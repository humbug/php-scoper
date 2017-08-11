<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor;

use ArrayIterator;
use function Humbug\PhpScoper\deep_clone;
use IteratorAggregate;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
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
     * Finds the statements matching the given name.
     *
     * $name = 'Foo';
     *
     * use X;
     * use Bar\Foo;
     * use Y;
     *
     * will return the use statement for `Bar\Foo`.
     *
     * @param string $name
     *
     * @return null|Name
     */
    public function findStatementForName(string $name): ?Name
    {
        foreach ($this->nodes as $use_) {
            foreach ($use_->uses as $useStatement) {
                if ($useStatement instanceof UseUse) {
                    if ($name === $useStatement->alias || $name === $useStatement->name->getLast()) {
                        return $useStatement->name;
                    }
                }

                //TODO
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->nodes);
    }
}