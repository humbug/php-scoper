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

use Humbug\PhpScoper\PhpParser\NodeVisitor\ClassAliasStmtAppender;
use Humbug\PhpScoper\PhpParser\NodeVisitor\ClassIdentifierRecorder;
use Humbug\PhpScoper\PhpParser\NodeVisitor\ConstStmtReplacer;
use Humbug\PhpScoper\PhpParser\NodeVisitor\EvalPrefixer;
use Humbug\PhpScoper\PhpParser\NodeVisitor\IdentifierNameAppender;
use Humbug\PhpScoper\PhpParser\NodeVisitor\MultiConstStmtReplacer;
use Humbug\PhpScoper\PhpParser\NodeVisitor\NamespaceStmt\FunctionIdentifierRecorder;
use Humbug\PhpScoper\PhpParser\NodeVisitor\NamespaceStmt\NamespaceStmtCollection;
use Humbug\PhpScoper\PhpParser\NodeVisitor\NamespaceStmt\NamespaceStmtPrefixer;
use Humbug\PhpScoper\PhpParser\NodeVisitor\NameStmtPrefixer;
use Humbug\PhpScoper\PhpParser\NodeVisitor\NewdocPrefixer;
use Humbug\PhpScoper\PhpParser\NodeVisitor\ParentNodeAppender;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\IdentifierResolver;
use Humbug\PhpScoper\PhpParser\NodeVisitor\StringScalarPrefixer;
use Humbug\PhpScoper\PhpParser\NodeVisitor\UseStmt\UseStmtCollection;
use Humbug\PhpScoper\PhpParser\NodeVisitor\UseStmt\UseStmtCollector;
use Humbug\PhpScoper\PhpParser\NodeVisitor\UseStmt\UseStmtPrefixer;
use Humbug\PhpScoper\Scoper\PhpScoper;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\NodeTraverser as PhpParserNodeTraverser;
use PhpParser\NodeTraverserInterface;
use PhpParser\NodeVisitor as PhpParserNodeVisitor;
use PhpParser\NodeVisitor\NameResolver;

/**
 * @private
 */
class TraverserFactory
{
    public function __construct(private readonly EnrichedReflector $reflector, private readonly string $prefix, private readonly SymbolsRegistry $symbolsRegistry)
    {
    }

    public function create(PhpScoper $scoper): NodeTraverserInterface
    {
        return self::createTraverser(
            self::createNodeVisitors(
                $this->prefix,
                $this->reflector,
                $scoper,
                $this->symbolsRegistry,
            ),
        );
    }

    /**
     * @param PhpParserNodeVisitor[] $nodeVisitors
     */
    private static function createTraverser(array $nodeVisitors): NodeTraverserInterface
    {
        $traverser = new NodeTraverser(
            new PhpParserNodeTraverser(),
        );

        foreach ($nodeVisitors as $nodeVisitor) {
            $traverser->addVisitor($nodeVisitor);
        }

        return $traverser;
    }

    /**
     * @return PhpParserNodeVisitor[]
     */
    private static function createNodeVisitors(
        string $prefix,
        EnrichedReflector $reflector,
        PhpScoper $scoper,
        SymbolsRegistry $symbolsRegistry
    ): array {
        $namespaceStatements = new NamespaceStmtCollection();
        $useStatements = new UseStmtCollection();

        $nameResolver = new NameResolver(
            null,
            ['preserveOriginalNames' => true],
        );
        $identifierResolver = new IdentifierResolver($nameResolver);
        $stringNodePrefixer = new StringNodePrefixer($scoper);

        return [
            $nameResolver,
            new ParentNodeAppender(),
            new IdentifierNameAppender($identifierResolver),

            new NamespaceStmtPrefixer(
                $prefix,
                $reflector,
                $namespaceStatements,
            ),

            new UseStmtCollector(
                $namespaceStatements,
                $useStatements,
            ),
            new UseStmtPrefixer(
                $prefix,
                $reflector,
            ),

            new FunctionIdentifierRecorder(
                $prefix,
                $identifierResolver,
                $symbolsRegistry,
                $reflector,
            ),
            new ClassIdentifierRecorder(
                $prefix,
                $identifierResolver,
                $symbolsRegistry,
                $reflector,
            ),
            new NameStmtPrefixer(
                $prefix,
                $namespaceStatements,
                $useStatements,
                $reflector,
            ),
            new StringScalarPrefixer(
                $prefix,
                $reflector,
            ),
            new NewdocPrefixer($stringNodePrefixer),
            new EvalPrefixer($stringNodePrefixer),

            new ClassAliasStmtAppender(
                $prefix,
                $reflector,
                $identifierResolver,
            ),
            new MultiConstStmtReplacer(),
            new ConstStmtReplacer(
                $identifierResolver,
                $reflector,
            ),
        ];
    }
}
