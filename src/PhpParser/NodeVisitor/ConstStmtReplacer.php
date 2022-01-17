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

use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\IdentifierResolver;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeVisitorAbstract;
use UnexpectedValueException;
use function count;

/**
 * Replaces constants `const` declarations by `define` when the constant is exposed.
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
    private IdentifierResolver $identifierResolver;
    private EnrichedReflector $enrichedReflector;

    public function __construct(
        IdentifierResolver $identifierResolver,
        EnrichedReflector $enrichedReflector
    ) {
        $this->identifierResolver = $identifierResolver;
        $this->enrichedReflector = $enrichedReflector;
    }

    public function enterNode(Node $node): Node
    {
        if (!$node instanceof Const_) {
            return $node;
        }

        foreach ($node->consts as $constant) {
            $replacement = $this->replaceConst($node, $constant);

            if (null !== $replacement) {
                return $replacement;
            }
        }

        return $node;
    }

    private function replaceConst(Const_ $const, Node\Const_ $constant): ?Node
    {
        $resolvedConstantName = $this->identifierResolver->resolveIdentifier(
            $constant->name,
        );

        if (!$this->enrichedReflector->isExposedConstant((string) $resolvedConstantName)) {
            return null;
        }

        if (count($const->consts) > 1) {
            throw new UnexpectedValueException(
                'Exposing a constant declared in a grouped constant statement (e.g. `const FOO = \'foo\', BAR = \'bar\'; is not supported. Consider breaking it down in multiple constant declaration statements',
            );
        }

        return self::createDefineNode(
            (string) $resolvedConstantName,
            $constant->value,
        );
    }

    private static function createDefineNode(string $resolvedConstantName, Expr $value): Node
    {
        return new Expression(
            new FuncCall(
                new FullyQualified('define'),
                [
                    new Arg(
                        new String_($resolvedConstantName)
                    ),
                    new Arg($value),
                ],
            ),
        );
    }
}
