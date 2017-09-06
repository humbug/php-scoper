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

use function Humbug\PhpScoper\deep_clone;
use Humbug\PhpScoper\NodeVisitor\Collection\NamespaceStmtCollection;
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
 */
final class NamespaceStmtPrefixer extends NodeVisitorAbstract
{
    private $prefix;
    private $namespaceStatements;

    public function __construct(string $prefix, NamespaceStmtCollection $namespaceStatements)
    {
        $this->prefix = $prefix;
        $this->namespaceStatements = $namespaceStatements;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if (false === ($node instanceof Namespace_)) {
            return $node;
        }
        /** @var Namespace_ $node */

        $originalNode = $node;

        if (null !== $node->name && $this->prefix !== $node->name->getFirst()) {
            $originalNode = deep_clone($node);

            $node->name = Name::concat($this->prefix, $node->name);
        }

        $this->namespaceStatements->add($node, $originalNode);

        return $node;
    }
}
