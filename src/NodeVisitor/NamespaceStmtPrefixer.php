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

use Humbug\PhpScoper\NodeVisitor\Collection\NamespaceStmtCollection;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;
use function Humbug\PhpScoper\clone_node;

/**
 * Prefixes the relevant namespaces.
 *
 * ```
 * namespace Foo;
 * ```
 *
 * =>
 *
 * ```
 * namespace Humbug\Foo;
 * ```
 */
final class NamespaceStmtPrefixer extends NodeVisitorAbstract
{
    private $prefix;
    private $namespaceStatements;
    private $hasWhitelistedNode;
    private $globalWhitelister;

    /**
     * @param string                  $prefix
     * @param NamespaceStmtCollection $namespaceStatements
     * @param callable                $globalWhitelister
     */
    public function __construct(
        string $prefix,
        NamespaceStmtCollection $namespaceStatements,
        callable $globalWhitelister
    ) {
        $this->prefix = $prefix;
        $this->namespaceStatements = $namespaceStatements;
        $this->globalWhitelister = $globalWhitelister;
    }

    /**
     * @inheritdoc
     */
    public function beforeTraverse(array $nodes)
    {
        $this->hasWhitelistedNode = $this->hasWhitelistedNode($nodes);
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        return ($node instanceof Namespace_)
            ? $this->prefixNamespaceStmt($node)
            : $node;
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        return (
            !$this->hasWhitelistedNode
            || $node instanceof Namespace_
            || AppendParentNode::hasParent($node)
        ) ? $node : $this->wrapNamespace($node);
    }

    private function prefixNamespaceStmt(Namespace_ $namespace): Node
    {
        $originalNamespace = $namespace;

        if ($this->shouldPrefixStmt($namespace)) {
            $originalNamespace = clone_node($namespace);

            $namespace->name = Name::concat($this->prefix, $namespace->name);
        }

        $this->namespaceStatements->add($namespace, $originalNamespace);

        return $namespace;
    }

    private function wrapNamespace(Node $node): Node
    {
        if ($this->isWhitelistedNode($node)) {
            return new Namespace_(new Node\Name($this->prefix), [$node]);
        }

        // Anything else needs to be wrapped with global namespace.
        return new Namespace_(null, [$node]);
    }

    /**
     * @param Node[] $nodes
     *
     * @return bool
     */
    private function hasWhitelistedNode(array $nodes): bool
    {
        foreach ($nodes as $node) {
            if ($this->isWhitelistedNode($node)) {
                return true;
            }
        }

        return false;
    }

    private function isWhitelistedNode(Node $node)
    {
        if (($node instanceof Class_ || $node instanceof Interface_)
            && ($this->globalWhitelister)($node->name)
        ) {
            return true;
        }

        if ($node instanceof Namespace_) {
            if (null !== $node->name) {
                return false;
            }

            foreach ($node->stmts as $statement) {
                if ($this->isWhitelistedNode($statement)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function shouldPrefixStmt(Namespace_ $namespace): bool
    {
        if (null !== $namespace->name && $this->prefix !== $namespace->name->getFirst()) {
            return true;
        }

        if (null === $namespace->name && $this->hasWhitelistedNode([$namespace])) {
            return true;
        }

        return false;
    }
}
