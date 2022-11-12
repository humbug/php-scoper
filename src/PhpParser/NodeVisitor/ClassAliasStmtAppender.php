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
use Humbug\PhpScoper\PhpParser\NodeVisitor\AttributeAppender\ParentNodeAppender;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\IdentifierResolver;
use Humbug\PhpScoper\PhpParser\UnexpectedParsingScenario;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\Node;
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
    public function __construct(
        private readonly IdentifierResolver $identifierResolver,
        private readonly SymbolsRegistry $symbolsRegistry,
    ) {
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
     * @param Stmt[] $stmts
     *
     * @return Stmt[]
     */
    private function createNamespaceStmts(array $stmts, Stmt $stmt): array
    {
        $stmts[] = $stmt;

        $isClassOrInterface = $stmt instanceof Class_ || $stmt instanceof Interface_;

        if (!$isClassOrInterface) {
            return $stmts;
        }

        $name = $stmt->name;

        if (null === $name) {
            throw UnexpectedParsingScenario::create();
        }

        $resolvedName = $this->identifierResolver->resolveIdentifier($name);

        if (!($resolvedName instanceof FullyQualified)) {
            return $stmts;
        }

        $record = $this->symbolsRegistry->getRecordedClass((string) $resolvedName);

        if (null !== $record) {
            $stmts[] = self::createAliasStmt($record[0], $record[1], $stmt);
        }

        return $stmts;
    }

    private static function createAliasStmt(
        string $originalName,
        string $prefixedName,
        Node $stmt
    ): Expression {
        $call = new ClassAliasFuncCall(
            new FullyQualified($prefixedName),
            new FullyQualified($originalName),
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
