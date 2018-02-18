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

namespace Humbug\PhpScoper\NodeVisitor;

use Humbug\PhpScoper\NodeVisitor\Resolver\FullyQualifiedNameResolver;
use Humbug\PhpScoper\Reflector;
use function is_string;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\NodeVisitorAbstract;

/**
 * ```
 * function foo(): void {}
 * ```.
 *
 * =>
 *
 * ```
 * function foo(): \void {}
 * ```
 *
 * @private
 */
final class FuncReturnPrefixer extends NodeVisitorAbstract
{
    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if (false === ($node instanceof Function_)) {
            return $node;
        }

        /** @var Function_ $node */
        if (is_string($node->returnType)) {
            $node->returnType = '\\'.$node->returnType;
        } elseif ($node->returnType instanceof NullableType && is_string($node->returnType->type)) {
            $node->returnType->type = '\\'.$node->returnType->type;
        }

        return $node;
    }
}
