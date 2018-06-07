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

use function get_class;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\FullyQualifiedNameResolver;
use Humbug\PhpScoper\Reflector;
use Humbug\PhpScoper\Whitelist;
use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\NodeVisitorAbstract;
use function is_string;
use function preg_match;

/**
 * Replaces const declaration by define
 *
 * ```
 * const DUMMY_CONST = 'foo';
 * ```
 *
 * =>
 *
 * ```
 * define('DUMMY_CONST', 'foo');
 * ```
 *
 * @private
 */
final class ConstStmtReplacer extends NodeVisitorAbstract
{
    private $whitelist;
    private $nameResolver;

    public function __construct(Whitelist $whitelist, FullyQualifiedNameResolver $nameResolver)
    {
        $this->whitelist = $whitelist;
        $this->nameResolver = $nameResolver;
    }

    /**
     * {@inheritdoc}
     *
     * @param Node\Stmt\Const_ $node
     */
    public function enterNode(Node $node): Node
    {
        if (false === ($node instanceof Node\Stmt\Const_)) {
            return $node;
        }

        // TODO: check this: when can Node\Stmt\Const_ be empty or have more than one constant
        /** @var Node\Const_ $constant */
        $constant = current($node->consts);

        $resolvedConstantName = $this->nameResolver->resolveName(
            new Name(
                (string) $constant->name,
                $node->getAttributes()  // Take the parent node attribute since no "parent" attribute is recorded in
                                        // Node\Const_
                                        // TODO: check with nikic if this is expected
            )
        )->getName();

        if (false === $this->whitelist->isClassWhitelisted((string) $resolvedConstantName)) {
            return $node;
        }

        return new Expression(
            new FuncCall(
                new FullyQualified('define'),
                [
                    new String_((string) $resolvedConstantName),
                    $constant->value
                ]
            )
        );
    }
}
