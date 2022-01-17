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

use Humbug\PhpScoper\PhpParser\Node\ClassAliasFuncCall;
use Humbug\PhpScoper\PhpParser\Node\FullyQualifiedFactory;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\IdentifierResolver;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;
use function array_reduce;

/**
 * Appends a `class_alias` statement to the exposed classes.
 *
 * ```
 * namespace A;
 *
 * class Foo
 * {
 * }
 * ```
 *
 * =>
 *
 * ```
 * namespace Humbug\A;
 *
 * class Foo
 * {
 * }
 *
 * class_alias('Humbug\A\Foo', 'A\Foo', false);
 * ```
 *
 * @internal
 */
final class ClassAliasStmtAppender extends NodeVisitorAbstract
{
    private string $prefix;
    private IdentifierResolver $identifierResolver;
    private EnrichedReflector $enrichedReflector;

    public function __construct(
        string $prefix,
        IdentifierResolver $identifierResolver,
        EnrichedReflector $enrichedReflector
    ) {
        $this->prefix = $prefix;
        $this->identifierResolver = $identifierResolver;
        $this->enrichedReflector = $enrichedReflector;
    }

    public function afterTraverse(array $nodes): array
    {
        $newNodes = [];

        foreach ($nodes as $node) {
            if ($node instanceof Namespace_) {
                $node = $this->appendToNamespaceStmt($node);
            }

            $newNodes[] = $node;
        }

        return $newNodes;
    }

    private function appendToNamespaceStmt(Namespace_ $namespace): Namespace_
    {
        $namespace->stmts = array_reduce(
            $namespace->stmts,
            fn (array $stmts, Stmt $stmt) => $this->createNamespaceStmts($stmts, $stmt),
            [],
        );

        return $namespace;
    }

    /**
     * @return Stmt[]
     */
    private function createNamespaceStmts(array $stmts, Stmt $stmt): array
    {
        $stmts[] = $stmt;

        if (!($stmt instanceof Class_ || $stmt instanceof Interface_)) {
            return $stmts;
        }

        $name = $stmt->name;

        if (null === $name) {
            return $stmts;
        }

        $resolvedName = $this->identifierResolver->resolveIdentifier($name);

        if ($resolvedName instanceof FullyQualified
            && $this->shouldAppendStmt($resolvedName)
        ) {
            $stmts[] = self::createAliasStmt($resolvedName, $stmt, $this->prefix);
        }

        return $stmts;
    }

    private function shouldAppendStmt(FullyQualified $resolvedName): bool
    {
        $resolvedNameString = (string) $resolvedName;

        return !$this->enrichedReflector->belongsToExcludedNamespace($resolvedNameString)
            && $this->enrichedReflector->isExposedClass($resolvedName);
    }

    private static function createAliasStmt(
        FullyQualified $originalName,
        Node $stmt,
        string $prefix
    ): Expression
    {
        $call = new ClassAliasFuncCall(
            FullyQualifiedFactory::concat($prefix, $originalName),
            $originalName,
            $stmt->getAttributes(),
        );

        $expression = new Expression(
            $call,
            $stmt->getAttributes(),
        );

        ParentNodeAppender::setParent($call, $expression);

        return $expression;
    }
}
