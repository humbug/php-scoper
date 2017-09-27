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
    private $whitelist;

    /**
     * @param string                  $prefix
     * @param NamespaceStmtCollection $namespaceStatements
     * @param string[]                $whitelist
     */
    public function __construct(string $prefix, NamespaceStmtCollection $namespaceStatements, array $whitelist)
    {
        $this->prefix = $prefix;
        $this->namespaceStatements = $namespaceStatements;
        $this->whitelist = $whitelist;
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

    private function shouldPrefixStmt(Namespace_ $namespace): bool
    {
        return null !== $namespace->name && $this->prefix !== $namespace->name->getFirst();
    }
}
