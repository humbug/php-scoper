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
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\FullyQualifiedNameResolver;
use Humbug\PhpScoper\Whitelist;
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
 * Appends a `class_alias` to the whitelisted classes.
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
    private $prefix;
    private $whitelist;
    private $nameResolver;

    public function __construct(string $prefix, Whitelist $whitelist, FullyQualifiedNameResolver $nameResolver)
    {
        $this->prefix = $prefix;
        $this->whitelist = $whitelist;
        $this->nameResolver = $nameResolver;
    }

    /**
     * @inheritdoc
     */
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
            [$this, 'createNamespaceStmts'],
            []
        );

        return $namespace;
    }

    /**
     * @return Stmt[]
     */
    private function createNamespaceStmts(array $stmts, Stmt $stmt): array
    {
        $stmts[] = $stmt;

        if (false === ($stmt instanceof Class_ || $stmt instanceof Interface_)) {
            return $stmts;
        }

        /** @var Class_|Interface_ $stmt */
        if (null === $stmt->name) {
            return $stmts;
        }

        $originalName = $this->nameResolver->resolveName($stmt->name)->getName();

        if (false === ($originalName instanceof FullyQualified)
            || $this->whitelist->belongsToWhitelistedNamespace((string) $originalName)
            || (
                false === $this->whitelist->isSymbolWhitelisted((string) $originalName)
                && false === $this->whitelist->isGlobalWhitelistedClass((string) $originalName)
            )
        ) {
            return $stmts;
        }
        /* @var FullyQualified $originalName */

        $stmts[] = $this->createAliasStmt($originalName, $stmt);

        return $stmts;
    }

    private function createAliasStmt(FullyQualified $originalName, Node $stmt): Expression
    {
        $call = new ClassAliasFuncCall(
            FullyQualifiedFactory::concat($this->prefix, $originalName),
            $originalName,
            $stmt->getAttributes()
        );

        $expression = new Expression(
            $call,
            $stmt->getAttributes()
        );

        $call->setAttribute(ParentNodeAppender::PARENT_ATTRIBUTE, $expression);

        return $expression;
    }
}
