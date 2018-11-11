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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor;

use Humbug\PhpScoper\PhpParser\NodeVisitor\Collection\NamespaceStmtCollection;
use Humbug\PhpScoper\Whitelist;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassLike;
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
 *
 * @private
 */
final class NamespaceStmtPrefixer extends NodeVisitorAbstract
{
    private $prefix;
    private $whitelist;
    private $namespaceStatements;

    public function __construct(string $prefix, Whitelist $whitelist, NamespaceStmtCollection $namespaceStatements)
    {
        $this->prefix = $prefix;
        $this->whitelist = $whitelist;
        $this->namespaceStatements = $namespaceStatements;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        return ($node instanceof Namespace_)
            ? $this->prefixNamespaceStmt($node)
            : $node
        ;
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

    private function isWhitelistedNode(Node $node): bool
    {
        if ($node instanceof ClassLike) {
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
        if ($this->whitelist->isWhitelistedNamespace((string) $namespace->name)) {
            return false;
        }

        return null === $namespace->name || (null !== $namespace->name && $this->prefix !== $namespace->name->getFirst());
    }
}
