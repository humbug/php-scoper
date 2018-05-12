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

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;

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
final class WhitelistedClassAppender extends NodeVisitorAbstract
{
    private $whitelist;

    /**
     * @param string[] $whitelists
     */
    public function __construct(array $whitelists)
    {
        $this->whitelist = $whitelists;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        return ($node instanceof Namespace_)
            ? $this->appendToNamespaceStmt($node)
            : $node;
    }

    private function appendToNamespaceStmt(Namespace_ $namespace): Namespace_
    {
        if (0 === count($this->whitelist)) {
            return $namespace;
        }

        $newStmts = [];

        foreach ($namespace->stmts as $stmt) {
            $newStmts[] = $stmt;

            if (false === ($stmt instanceof Class_ || $stmt instanceof Interface_)) {
                continue;
            }

            /** @var Class_ $stmt */
            $name = FullyQualified::concat((string) $namespace->name, (string) $stmt->name);
            $originalName = $name->slice(1);

            if (false === in_array((string) $originalName, $this->whitelist, true)) {
                continue;
            }

            $newStmts[] = new Expression(new FuncCall(
                new Name('class_alias'),
                [
                    new Arg(
                        new String_((string) $name)
                    ),
                    new Arg(
                        new String_((string) $originalName)
                    ),
                    new Arg(
                        new ConstFetch(
                            new FullyQualified('false')
                        )
                    ),
                ],
                ['whitelist_class_alias' => true]
            ));
        }

        $namespace->stmts = $newStmts;

        return $namespace;
    }
}
