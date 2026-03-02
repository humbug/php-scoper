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
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\NodeVisitorAbstract;
use function array_reduce;
use function in_array;

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

    /**
     * @param Node[] $nodes
     *
     * @return Node[]
     */
    public function afterTraverse(array $nodes): array
    {
        $this->traverseNodes($nodes);

        return $nodes;
    }

    /**
     * @param Node[] $nodes
     */
    private function traverseNodes(array $nodes): void
    {
        foreach ($nodes as $node) {
            if (self::isNodeAStatementWithStatements($node)) {
                $this->updateStatements($node);
            }
        }
    }

    /**
     * @phpstan-assert-if-true Stmt $node
     */
    private static function isNodeAStatementWithStatements(Node $node): bool
    {
        return $node instanceof Stmt && in_array('stmts', $node->getSubNodeNames(), true);
    }

    /**
     * @template T of Stmt
     *
     * @param T|null $statement
     */
    private function updateStatements(?Stmt $statement): void
    {
        if (null === $statement || null === $statement->stmts) {
            return;
        }

        $statement->stmts = array_reduce(
            $statement->stmts,
            fn (array $stmts, Stmt $stmt) => $this->appendClassAliasStmtIfApplicable($stmts, $stmt),
            [],
        );
    }

    /**
     * @param Stmt[] $stmts
     *
     * @return Stmt[]
     */
    private function appendClassAliasStmtIfApplicable(array $stmts, Stmt $stmt): array
    {
        $stmts[] = $stmt;

        $isClassOrInterface = $stmt instanceof Class_ || $stmt instanceof Interface_;

        if ($isClassOrInterface) {
            return $this->appendClassAliasStmtIfNecessary($stmts, $stmt);
        }

        /** @phpstan-ignore staticMethod.alreadyNarrowedType */
        if (self::isNodeAStatementWithStatements($stmt)) {
            $this->updateStatements($stmt);
        }

        if ($stmt instanceof If_) {
            $this->updateStatements($stmt->else);
            $this->traverseNodes($stmt->elseifs);
        } elseif ($stmt instanceof Switch_) {
            $this->traverseNodes($stmt->cases);
        } elseif ($stmt instanceof TryCatch) {
            $this->traverseNodes($stmt->catches);
            $this->updateStatements($stmt->finally);
        }

        return $stmts;
    }

    /**
     * @param Stmt[] $stmts
     *
     * @return Stmt[]
     */
    private function appendClassAliasStmtIfNecessary(array $stmts, Class_|Interface_ $stmt): array
    {
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
        Node $stmt,
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
