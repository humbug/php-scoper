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

namespace Humbug\PhpScoper\PhpParser;

use Humbug\PhpScoper\PhpParser\NodeVisitor\Collection\NamespaceStmtCollection;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Collection\UseStmtCollection;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\FullyQualifiedNameResolver;
use Humbug\PhpScoper\Reflector;
use PhpParser\NodeTraverserInterface;

/**
 * @private
 */
class TraverserFactory
{
    private $reflector;

    public function __construct(Reflector $reflector)
    {
        $this->reflector = $reflector;
    }

    public function create(string $prefix, array $whitelist): NodeTraverserInterface
    {
        $traverser = new NodeTraverser($prefix);

        $namespaceStatements = new NamespaceStmtCollection();
        $useStatements = new UseStmtCollection();

        $nameResolver = new FullyQualifiedNameResolver($namespaceStatements, $useStatements);

        $traverser->addVisitor(new NodeVisitor\AppendParentNode());

        $traverser->addVisitor(new NodeVisitor\NamespaceStmtPrefixer($prefix, $namespaceStatements));

        $traverser->addVisitor(new NodeVisitor\UseStmt\UseStmtCollector($namespaceStatements, $useStatements));
        $traverser->addVisitor(new NodeVisitor\UseStmt\UseStmtPrefixer($prefix, $this->reflector));

        $traverser->addVisitor(new NodeVisitor\NameStmtPrefixer($prefix, $nameResolver, $this->reflector));
        $traverser->addVisitor(new NodeVisitor\StringScalarPrefixer($prefix, $this->reflector));

        $traverser->addVisitor(new NodeVisitor\WhitelistedClassAppender($whitelist));

        return $traverser;
    }
}
