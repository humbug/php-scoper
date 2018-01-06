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
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Interface_;
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
    private $globalWhitelister;

    /**
     * @param string   $prefix              Global prefix to apply.
     * @param callable $globalWhitelister   List of whitelisted nodes.
     */
    public function __construct(
        string $prefix,
        callable $globalWhitelister
    ) {
        $this->prefix = $prefix;
        $this->globalWhitelister = $globalWhitelister;
    }

    /**
     * @inheritdoc
     */
    public function beforeTraverse(array $nodes)
    {
        $classes = array_filter($nodes, [$this, 'isWhitelistableNode']);
        if ([] === $classes) {
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
     */
    private function wrapClassNamespace(Node $node): Node
    {
        if (AppendParentNode::hasParent($node)) {
            return $node;
        }

        if (
            $node instanceof ClassLike
            && $this->isWhitelistableNode($node)
            && ($this->globalWhitelister)($node->name)
        ) {
            return new Namespace_(new Node\Name($this->prefix), [$node]);
        }

        // Anything else needs to be wrapped with global namespace.
        return new Namespace_(null, [$node]);
    }

    /**
     */
    private function hasWhitelistedNode(array $nodes): bool
    {
        $nodes = array_filter($nodes, function ($node) {
            return null !== $node->name && ($this->globalWhitelister)($node->name);
        });

        return !empty($nodes);
    }

    /**
     */
    private function isWhitelistableNode(Node $node): bool
    {
        return $node instanceof Class_ || $node instanceof Interface_;
    }
}
