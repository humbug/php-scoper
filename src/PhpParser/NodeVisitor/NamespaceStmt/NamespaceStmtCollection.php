<?php

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser\NodeVisitor\NamespaceStmt;

use ArrayIterator;
use Countable;
use Humbug\PhpScoper\PhpParser\NodeVisitor\ParentNodeAppender;
use Humbug\PhpScoper\PhpParser\UnexpectedParsingScenario;
use IteratorAggregate;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use Traversable;
use function count;
use function end;

/**
 * Utility class collecting all the namespaces for the scoped files allowing to easily find the namespace to which
 * belongs a node.
 *
 * @private
 */
final class NamespaceStmtCollection implements IteratorAggregate, Countable
{
    /**
     * @var Namespace_[]
     */
    private array $nodes = [];

    /**
     * @var (Name|null)[] Associative array with the potentially prefixed namespace names as keys and their original name
     *                    as value
     */
    private array $mapping = [];

    /**
     * @param Namespace_ $namespace new namespace, may have been prefixed
     */
    public function add(Namespace_ $namespace): void
    {
        $this->nodes[] = $namespace;

        $this->mapping[(string) $namespace->name] = NamespaceManipulator::getOriginalName($namespace);
    }

    public function findNamespaceForNode(Node $node): ?Name
    {
        if (0 === count($this->nodes)) {
            return null;
        }

        // Shortcut if there is only one namespace
        if (1 === count($this->nodes)) {
            return NamespaceManipulator::getOriginalName($this->nodes[0]);
        }

        return $this->getNodeNamespaceName($node);
    }

    public function getCurrentNamespaceName(): ?Name
    {
        $lastNode = end($this->nodes);

        return false === $lastNode ? null : NamespaceManipulator::getOriginalName($lastNode);
    }

    public function count(): int
    {
        return count($this->nodes);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->nodes);
    }

    private function getNodeNamespaceName(Node $node): ?Name
    {
        if (!ParentNodeAppender::hasParent($node)) {
            throw UnexpectedParsingScenario::create();
        }

        $parentNode = ParentNodeAppender::getParent($node);

        if ($parentNode instanceof Namespace_) {
            return $this->mapping[(string) $parentNode->name];
        }

        return $this->getNodeNamespaceName($parentNode);
    }
}
