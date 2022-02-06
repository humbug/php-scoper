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
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\IdentifierResolver;
use Humbug\PhpScoper\PhpParser\NodeVisitor\UseStmt\UseStmtCollection;
use Humbug\PhpScoper\Scoper\PhpScoper;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitor as PhpParserNodeVisitor;
use PhpParser\NodeVisitor\NameResolver;

/**
 * @private
 */
class TraverserFactory
{
    private EnrichedReflector $reflector;

    public function __construct(EnrichedReflector $reflector)
    {
        $this->reflector = $reflector;
    }

    public function create(
        PhpScoper $scoper,
        string $prefix,
        SymbolsRegistry $symbolsRegistry
    ): NodeTraverserInterface
    {
        $traverser = new NodeTraverser();

        $namespaceStatements = new NamespaceStmtCollection();
        $useStatements = new UseStmtCollection();

        $nameResolver = new NameResolver(
            null,
            ['preserveOriginalNames' => true],
        );
        $identifierResolver = new IdentifierResolver($nameResolver);
        $stringNodePrefixer = new StringNodePrefixer($scoper);

        self::addVisitors(
            $traverser,
            [
                $nameResolver,
                new NodeVisitor\ParentNodeAppender(),
                new NodeVisitor\IdentifierNameAppender($identifierResolver),

                new NodeVisitor\NamespaceStmt\NamespaceStmtPrefixer(
                    $prefix,
                    $this->reflector,
                    $namespaceStatements,
                ),

                new NodeVisitor\UseStmt\UseStmtCollector(
                    $namespaceStatements,
                    $useStatements,
                ),
                new NodeVisitor\UseStmt\UseStmtPrefixer(
                    $prefix,
                    $this->reflector,
                ),

                new NodeVisitor\NamespaceStmt\FunctionIdentifierRecorder(
                    $prefix,
                    $identifierResolver,
                    $symbolsRegistry,
                    $this->reflector,
                ),
                new NodeVisitor\ClassIdentifierRecorder(
                    $prefix,
                    $identifierResolver,
                    $symbolsRegistry,
                    $this->reflector,
                ),
                new NodeVisitor\NameStmtPrefixer(
                    $prefix,
                    $namespaceStatements,
                    $useStatements,
                    $this->reflector,
                ),
                new NodeVisitor\StringScalarPrefixer(
                    $prefix,
                    $this->reflector,
                ),
                new NodeVisitor\NewdocPrefixer($stringNodePrefixer),
                new NodeVisitor\EvalPrefixer($stringNodePrefixer),

                new NodeVisitor\ClassAliasStmtAppender(
                    $prefix,
                    $this->reflector,
                    $identifierResolver,
                ),
                new NodeVisitor\MultiConstStmtReplacer(),
                new NodeVisitor\ConstStmtReplacer(
                    $identifierResolver,
                    $this->reflector,
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
