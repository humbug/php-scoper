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

use Humbug\PhpScoper\PhpParser\NodeVisitor\NamespaceStmt\NamespaceStmtCollection;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\FullyQualifiedNameResolver;
use Humbug\PhpScoper\PhpParser\NodeVisitor\UseStmt\UseStmtCollection;
use Humbug\PhpScoper\Reflector;
use Humbug\PhpScoper\Scoper\PhpScoper;
use Humbug\PhpScoper\Whitelist;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitor as PhpParserNodeVisitor;
use PhpParser\NodeVisitor\NameResolver;

/**
 * @private
 */
class TraverserFactory
{
    private Reflector $reflector;

    public function __construct(Reflector $reflector)
    {
        $this->reflector = $reflector;
    }

    public function create(PhpScoper $scoper, string $prefix, Whitelist $whitelist): NodeTraverserInterface
    {
        $traverser = new NodeTraverser();

        $namespaceStatements = new NamespaceStmtCollection();
        $useStatements = new UseStmtCollection();

        $nameResolver = new FullyQualifiedNameResolver($namespaceStatements, $useStatements);
        $newNameResolver = new NameResolver();

        self::addVisitors(
            $traverser,
            [
                $newNameResolver,
                new NodeVisitor\ParentNodeAppender(),

                new NodeVisitor\NamespaceStmt\NamespaceStmtPrefixer(
                    $prefix,
                    $whitelist,
                    $namespaceStatements,
                ),

                new NodeVisitor\UseStmt\UseStmtCollector(
                    $namespaceStatements,
                    $useStatements,
                ),
                new NodeVisitor\UseStmt\UseStmtPrefixer(
                    $prefix,
                    $whitelist,
                    $this->reflector,
                ),

                new NodeVisitor\NamespaceStmt\FunctionIdentifierRecorder(
                    $prefix,
                    $nameResolver,
                    $newNameResolver,
                    $whitelist,
                    $this->reflector,
                ),
                new NodeVisitor\ClassIdentifierRecorder(
                    $prefix,
                    $nameResolver,
                    $newNameResolver,
                    $whitelist
                ),
                new NodeVisitor\NameStmtPrefixer(
                    $prefix,
                    $whitelist,
                    $namespaceStatements,
                    $useStatements,
                    $nameResolver,
                    $newNameResolver,
                    $this->reflector,
                ),
                new NodeVisitor\StringScalarPrefixer(
                    $prefix,
                    $whitelist,
                    $this->reflector,
                ),
                new NodeVisitor\NewdocPrefixer(
                    $scoper,
                    $prefix,
                    $whitelist,
                ),
                new NodeVisitor\EvalPrefixer(
                    $scoper,
                    $prefix,
                    $whitelist,
                ),

                new NodeVisitor\ClassAliasStmtAppender(
                    $prefix,
                    $whitelist,
                    $nameResolver,
                    $newNameResolver,
                ),
                new NodeVisitor\ConstStmtReplacer(
                    $whitelist,
                    $nameResolver,
                    $newNameResolver,
                ),
            ],
        );

        return $traverser;
    }

    /**
     * @param PhpParserNodeVisitor[] $nodeVisitors
     */
    private static function addVisitors(
        NodeTraverserInterface $nodeTraverser,
        array $nodeVisitors
    ): void
    {
        foreach ($nodeVisitors as $nodeVisitor) {
            $nodeTraverser->addVisitor($nodeVisitor);
        }
    }
}
