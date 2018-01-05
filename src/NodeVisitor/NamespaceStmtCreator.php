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
    protected $has_class_definition = false;
    private $prefix;

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

    public function beforeTraverse(array $nodes)
    {
        $classes = array_filter($nodes, function ($node) {
            return $node instanceof Class_;
        });

        if (empty($classes)) {
            return;
        }

        // Group and prepare classes.
        $classes = array_filter($classes, function ($class_node) {
            if (null === $class_node->name) {
                return false;
            }

            return ($this->globalWhitelister)($class_node->name);
        });

        if (empty($classes)) {
            return;
        }

        $this->has_class_definition = true;
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        if (! $this->has_class_definition) {
            return $node;
        }


        return $this->wrapClassNamespace($node);
    }

    /**
     * @param Class_ $node
     *
     * @return Node
     */
    private function wrapClassNamespace(Node $node): Node
    {
        $namespace = $this->namespaceStatements->findNamespaceForNode($node);
        if (null !== $namespace) {
            return $node;
        }

        if (true === AppendParentNode::hasParent($node)) {
            return $node;
        }

        if (! ($node instanceof Class_)) {
            return new Namespace_(null, [$node]);
        }

        if (null === $node->name) {
            return $node;
        }

        if (! ($this->globalWhitelister)($node->name)) {
            return new Namespace_(null, [$node]);
        }

        return new Namespace_(new Node\Name($this->prefix), [$node]);
    }
}
