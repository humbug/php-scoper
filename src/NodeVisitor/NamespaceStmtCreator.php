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
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt\Namespace_;

/**
 * Creates a namespace for whitelisted classes
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
    private $namespaceStatements;
    private $globalWhitelister;

    /**
     *
     *
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
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        return ($node instanceof Class_)
            ? $this->addNamespaceStmt($node)
            : $node;
    }

    /**
     * @param Class_ $node
     *
     * @return Node
     */
    private function addNamespaceStmt(Class_ $node): Node
    {
        $namespace = $this->namespaceStatements->findNamespaceForNode($node);
        if (null !== $namespace) {
            return $node;
        }

        if (null === $node->name) {
            return $node;
        }

        if (! ($this->globalWhitelister)($node->name)) {
            return $node;
        }

        return new Namespace_(new Node\Name($this->prefix), [$node]);
    }

}
