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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor\NamespaceStmt;

use Humbug\PhpScoper\Whitelist;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;

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
        if ($this->shouldPrefixStmt($namespace)) {
            $originalName = $namespace->name;

            $namespace->name = Name::concat($this->prefix, $namespace->name);

            NamespaceManipulator::setOriginalName($namespace, $originalName);
        }

        $this->namespaceStatements->add($namespace);

        return $namespace;
    }

    private function shouldPrefixStmt(Namespace_ $namespace): bool
    {
        if ($this->whitelist->isWhitelistedNamespace((string) $namespace->name)) {
            return false;
        }

        $nameFirstPart = null === $namespace->name ? '' : $namespace->name->getFirst();

        return $this->prefix !== $nameFirstPart;
    }
}
