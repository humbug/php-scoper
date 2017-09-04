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

namespace Humbug\PhpScoper\NodeVisitor;

use ArrayIterator;
use IteratorAggregate;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use function Humbug\PhpScoper\deep_clone;

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
    public function findStatementForName(string $name)
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
