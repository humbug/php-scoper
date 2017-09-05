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

namespace Humbug\PhpScoper\NodeVisitor\Collection;

use ArrayIterator;
use Countable;
use Humbug\PhpScoper\NodeVisitor\AppendParentNode;
use InvalidArgumentException;
use IteratorAggregate;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use function Humbug\PhpScoper\deep_clone;

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

    public function findNamespaceForNode(Node $node): ?Name
    {
        if (0 === count($this->nodes)) {
            return null;
        }

        if (1 < count($this->nodes)) {
            return $this->getNodeNamespace($node);
        }

        return $this->nodes[0]->name;
    }

    private function getNodeNamespace(Node $node): ?Name
    {
        if (false === AppendParentNode::hasParent($node)) {
            return null;
        }

        $parentNode = AppendParentNode::getParent($node);

        if ($parentNode instanceof Namespace_) {
            return $parentNode->name;
        }

        return $this->getNodeNamespace($parentNode);
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
