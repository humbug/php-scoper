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

    public function __construct(string $prefix, NamespaceStmtCollection $namespaceStatements)
    {
        $this->prefix = $prefix;
        $this->namespaceStatements = $namespaceStatements;
    }

//    /**
//     * @inheritdoc
//     */
//    public function beforeTraverse(array $nodes)
//    {
//        $this->hasWhitelistedNode = $this->hasWhitelistedNode($nodes);
//    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        $x = '';

        return ($node instanceof Namespace_)
            ? $this->prefixNamespaceStmt($node)
            : $node;
    }
//
//    /**
//     * @inheritdoc
//     */
//    public function leaveNode(Node $node)
//    {
//        $x = '';
//
//        return (
//            0 === $this->namespaceStatements->count()
//            && false === AppendParentNode::hasParent($node)
//        ) ? $this->wrapNamespace($node) : $node;
//    }

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
        return new Namespace_(new Node\Name($this->prefix), [$node]);
        if ($this->isWhitelistedNode($node)) {
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
        if (($node instanceof Class_ || $node instanceof Interface_)) {
            return true;
        }

        // Check nodes in the global namespaces.
        if ($node instanceof Namespace_ && null === $node->name) {
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
        return null === $namespace->name || (null !== $namespace->name && $this->prefix !== $namespace->name->getFirst());

        if (null !== $namespace->name && $this->prefix !== $namespace->name->getFirst()) {
            return true;
        }

//        if (null === $namespace->name && $this->hasWhitelistedNode([$namespace])) {
//            return true;
//        }

        return false;
    }
}
