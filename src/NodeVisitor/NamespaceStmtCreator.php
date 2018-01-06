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
 * Creates a namespace for whitelisted classes.
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

    private $hasWhitelistedClass;

    private $namespaceStatements;

    private $globalWhitelister;

    /**
     * @param string $prefix
     * @param NamespaceStmtCollection $namespaceStatements
     * @param callable $globalWhitelister
     */
    public function __construct(
        string $prefix,
        NamespaceStmtCollection $namespaceStatements,
        callable $globalWhitelister
    ) {
        $this->prefix              = $prefix;
        $this->namespaceStatements = $namespaceStatements;
        $this->globalWhitelister   = $globalWhitelister;
    }

    /**
     * @param array $nodes
     *
     * @return void
     */
    public function beforeTraverse(array $nodes): void
    {
        $classes = $this->getClasses($nodes);
        if (empty($classes)) {
            return;
        }

        $this->hasWhitelistedClass = $this->hasWhitelistedClass($classes);
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        if (! $this->hasWhitelistedClass) {
            return $node;
        }


        return $this->wrapClassNamespace($node);
    }

    /**
     * @param Node $node
     *
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

        if (! ($node instanceof Class_)) {
            return new Namespace_(null, [$node]);
        }

        // Whitelisted classes need to be wrapped with prefix namespace.
        if (null !== $node->name && ($this->globalWhitelister)($node->name)) {
            return new Namespace_(new Node\Name($this->prefix), [$node]);
        }

        // Anything else needs to be wrapped with global namespace.
        return new Namespace_(null, [$node]);
    }

    /**
     * @param Node $node
     *
     * @return bool
     */
    private function hasNamespace(Node $node): bool
    {
        return (null !== $this->namespaceStatements->findNamespaceForNode($node));
    }

    /**
     * @param $classes
     *
     * @return bool
     */
    private function hasWhitelistedClass(array $classes): bool
    {
        $classes = array_filter($classes, function ($class_node) {
            if (null === $class_node->name) {
                return false;
            }

            return ($this->globalWhitelister)($class_node->name);
        });

        return ! empty($classes);
    }

    /**
     * @param array $nodes
     *
     * @return array
     */
    private function getClasses(array $nodes): array
    {
        $classes = array_filter($nodes, function ($node) {
            return $node instanceof Class_;
        });

        return $classes;
    }
}
