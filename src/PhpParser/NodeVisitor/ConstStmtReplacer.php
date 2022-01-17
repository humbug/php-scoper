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
use Humbug\PhpScoper\Reflector;
use Humbug\PhpScoper\Whitelist;
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
 * Replaces constants `const` declarations by `define` for exposed constants.
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
    private Whitelist $whitelist;
    private IdentifierResolver $identifierResolver;
    private Reflector $reflector;

    public function __construct(
        Whitelist $whitelist,
        IdentifierResolver $identifierResolver,
        Reflector $reflector
    ) {
        $this->whitelist = $whitelist;
        $this->identifierResolver = $identifierResolver;
        $this->reflector = $reflector;
    }

    public function enterNode(Node $node): Node
    {
        if (!$node instanceof Const_) {
            return $node;
        }

        foreach ($node->consts as $constant) {
            $replacement = $this->replaceConst($node, $constant);

            if (null !== $replacement) {
                // If there is more than one constant declare in the node we
                // will not have a replacement (this case is not supported)
                // hence the return statement is safe here.
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

        if (!$this->isExposedConstant((string) $resolvedConstantName)) {
            // No replacement
            return null;
        }

        if (count($const->consts) > 1) {
            throw new UnexpectedValueException(
                'Exposing a constant declared in a grouped constant statement (e.g. `const FOO = \'foo\', BAR = \'bar\'; is not supported. Consider breaking it down in multiple constant declaration statements',
            );
        }

        return self::createConstDefineNode(
            (string) $resolvedConstantName,
            $constant->value,
        );
    }

    private static function createConstDefineNode(string $name, Expr $value): Node
    {
        return new Expression(
            new FuncCall(
                new FullyQualified('define'),
                [
                    new Arg(
                        new String_($name)
                    ),
                    new Arg($value),
                ],
            ),
        );
    }

    private function isExposedConstant(string $name): bool
    {
        // Special case: internal constants must be treated as exposed symbols.
        //
        // Example: when declaring a new internal constant for compatibility
        // reasons, it must remain un-prefixed.
        return $this->reflector->isConstantInternal($name)
            || $this->whitelist->isExposedConstantFromGlobalNamespace($name)
            || $this->whitelist->isSymbolExposed($name, true);
    }
}
