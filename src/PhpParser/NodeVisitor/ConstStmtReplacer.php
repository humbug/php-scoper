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

use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\FullyQualifiedNameResolver;
use Humbug\PhpScoper\Whitelist;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;
use UnexpectedValueException;
use function count;

/**
 * Replaces const declaration by define when the constant is whitelisted.
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
 * It does not do the prefixing.
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
     * @param Const_ $node
     */
    public function enterNode(Node $node): Node
    {
        if (!$node instanceof Const_) {
            return $node;
        }

        foreach ($node->consts as $constant) {
            /** @var Node\Const_ $constant */
            $resolvedConstantName = $this->nameResolver->resolveName(
                new Name(
                    (string) $constant->name,
                    $node->getAttributes()
                )
            )->getName();

            if (false === $this->whitelist->isSymbolWhitelisted((string) $resolvedConstantName, true)) {
                continue;
            }

            if (count($node->consts) > 1) {
                throw new UnexpectedValueException(
                    'Whitelisting a constant declared in a grouped constant statement (e.g. `const FOO = '
                    .'\'foo\', BAR = \'bar\'; is not supported. Consider breaking it down in multiple constant '
                    .'declaration statements'
                );
            }

            return new Expression(
                new FuncCall(
                    new FullyQualified('define'),
                    [
                        new Arg(
                            new String_((string) $resolvedConstantName)
                        ),
                        new Arg($constant->value),
                    ]
                )
            );
        }

        return $node;
    }
}
