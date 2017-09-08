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

namespace Humbug\PhpScoper\Scoper\TraverserFactory;

use Humbug\PhpScoper\NodeVisitor;
use Humbug\PhpScoper\NodeVisitor\Collection\NamespaceStmtCollection;
use Humbug\PhpScoper\NodeVisitor\Collection\UseStmtCollection;
use Humbug\PhpScoper\NodeVisitor\Resolver\FullyQualifiedNameResolver;
use Humbug\PhpScoper\Scoper\TraverserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;

final class NativeTraverserFactory implements TraverserFactory
{
    /**
     * Functions for which the arguments will be prefixed.
     */
    const WHITELISTED_FUNCTIONS = [
        'class_exists',
        'interface_exists',
    ];

    private $traverser;

    /**
     * @inheritdoc
     */
    public function create(string $prefix, array $whitelist, callable $globalWhitelister): NodeTraverserInterface
    {
        if (null !== $this->traverser) {
            return $this->traverser;
        }

        $this->traverser = new NodeTraverser();

        $namespaceStatements = new NamespaceStmtCollection();
        $useStatements = new UseStmtCollection();

        $nameResolver = new FullyQualifiedNameResolver($namespaceStatements, $useStatements);

        $this->traverser->addVisitor(new NodeVisitor\UseStmt\GroupUseStmtTransformer());

        $this->traverser->addVisitor(new NodeVisitor\AppendParentNode());

        $this->traverser->addVisitor(new NodeVisitor\NamespaceStmtPrefixer($prefix, $namespaceStatements));

        $this->traverser->addVisitor(new NodeVisitor\UseStmt\UseStmtCollector($namespaceStatements, $useStatements));
        $this->traverser->addVisitor(new NodeVisitor\UseStmt\UseStmtPrefixer($prefix, $whitelist, $globalWhitelister));

        $this->traverser->addVisitor(new NodeVisitor\NameStmtPrefixer($prefix, $whitelist, $globalWhitelister, $nameResolver));
        $this->traverser->addVisitor(new NodeVisitor\StringScalarPrefixer($prefix, $whitelist, $globalWhitelister, $nameResolver));

        return $this->traverser;
    }
}
