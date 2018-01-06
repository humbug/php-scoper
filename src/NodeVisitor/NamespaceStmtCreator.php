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
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;

/**
 * Creates a namespace for whitelisted classes and interfaces which belong to the global namespace.
 *
 * ```
 * class AppKernel {}
 * ```
 *
 * =>
 *
 * ```
 * namespace Humbug;
 *
 * class AppKernel {}
 * ```
 */
final class NamespaceStmtCreator extends NodeVisitorAbstract
{
    private $prefix;
    private $hasWhitelistedNode;
    private $namespaceStatements;
    private $globalWhitelister;

    /**
     * @param string                  $prefix              Global prefix to apply.
     * @param NamespaceStmtCollection $namespaceStatements List of statements in the current scope.
     * @param callable                $globalWhitelister   List of whitelisted nodes.
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
        $classes = array_filter($nodes, [$this, 'isWhitelistableNode']);
        if (empty($classes)) {
            return;
        }

        $this->hasWhitelistedNode = $this->hasWhitelistedNode($classes);
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        return $this->hasWhitelistedNode ? $this->wrapClassNamespace($node) : $node;
    }

    /**
     * @return Node
     */
    private function wrapClassNamespace(Node $node): Node
    {
        if ($this->hasNamespace($node)) {
            return $node;
        }

        if (AppendParentNode::hasParent($node)) {
            return $node;
        }

        if (
            null !== $node->name
            && $this->isWhitelistableNode($node)
            && ($this->globalWhitelister)($node->name)
        ) {
            return new Namespace_(new Node\Name($this->prefix), [$node]);
        }

        // Anything else needs to be wrapped with global namespace.
        return new Namespace_(null, [$node]);
    }

    /**
     * @return bool
     */
    private function hasNamespace(Node $node): bool
    {
        return null !== $this->namespaceStatements->findNamespaceForNode($node);
    }

    /**
     * @param Node[] $nodes List of nodes to find whitelisted nodes from.
     *
     * @return bool
     */
    private function hasWhitelistedNode(array $nodes): bool
    {
        $nodes = array_filter($nodes, function ($node) {
            return null !== $node->name && ($this->globalWhitelister)($node->name);
        });

        return !empty($nodes);
    }

    /**
     * @return bool
     */
    private function isWhitelistableNode(Node $node): bool
    {
        return $node instanceof Class_ || $node instanceof Node\Stmt\Interface_;
    }
}
