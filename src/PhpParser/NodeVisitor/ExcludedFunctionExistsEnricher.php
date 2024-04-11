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
use Infection\Str;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp\BooleanAnd;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
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
 * Ensures that when a `function_exists` function call for an excluded function within an if
 * statement is found, it adds another `function_exists` call to the if statement.
 *
 * This is to ensure function declaration polyfills work fine even if several declarations
 * can be found.
 *
 * For example:
 *
 * ```
 * if (!function_exists('str_replace')) {
 *     function str_replace() { ... }
 * }
 * ```
 *
 * Will be changed to:
 *
 * ```
 * if (!function_exists('str_replace') && !function_exists('Humbug\str_replace')) {
 *     function str_replace() { ... }
 * }
 * ```
 *
 * @internal
 */
final class ExcludedFunctionExistsEnricher extends NodeVisitorAbstract
{
    public function __construct(
        private readonly string $prefix,
        private readonly ExcludedFunctionExistsStringNodeStack $excludedFunctionExistsStringNodeStack,
    ) {
    }

    public function afterTraverse(array $nodes): array
    {
        $strings = $this->excludedFunctionExistsStringNodeStack->fetch();

        foreach ($strings as $string) {
            self::addScopedFunctionExistsCondition(
                $this->prefix,
                $string,
            );
        }

        return $nodes;
    }

    private static function addScopedFunctionExistsCondition(string $prefix, String_ $string): void
    {
        [$ifStmt, $ifCondition, $boolExpr, $funcCall] = self::findFlattenedParentIfStmt($string);

        if (null === $ifStmt) {
            return;
        }

        self::replaceCondition(
            $prefix,
            $ifStmt,
            $ifCondition,
            $boolExpr,
            $funcCall,
            $string,
        );
    }

    /**
     * Returns the if statement node if the structure is the following one:
     *
     * Stmt_If
     *   + cond: Expr_BooleanNot
     *       + exp: Expr_FuncCall(function_exists)
     *
     * @return null|array{If_, Expr|null, BooleanNot, FuncCall}
     */
    private static function findFlattenedParentIfStmt(String_ $string): ?array
    {
        $funcCallArg = ParentNodeAppender::getParent($string);

        if (!($funcCallArg instanceof Arg)) {
            return null;
        }

        $funcCall = ParentNodeAppender::getParent($funcCallArg);

        if (!($funcCall instanceof FuncCall) || (string) $funcCall->name !== 'function_exists') {
            return null;
        }

        $boolExpr = ParentNodeAppender::getParent($funcCall);

        // TODO: check for other expr
        if (!($boolExpr instanceof BooleanNot)
            && !($boolExpr instanceof Identical)
            && !($boolExpr instanceof Equal)
        ) {
            return null;
        }

        $ifStmtOrExpr = ParentNodeAppender::getParent($boolExpr);

        if ($ifStmtOrExpr instanceof Expr) {
            $ifCondition = $ifStmtOrExpr;

            $ifStmt = ParentNodeAppender::getParent($ifStmtOrExpr);
        } else {
            $ifCondition = null;
            $ifStmt = $ifStmtOrExpr;
        }

        if (!($ifStmt instanceof If_)) {
            return null;
        }

        return [$ifStmt, $ifCondition, $boolExpr, $funcCall];
    }

    /**
     * @template T of Stmt
     *
     * @param Stmt|null $statement
     */
    private static function replaceCondition(
        string $prefix,
        If_ $ifStmt,
        ?Expr $ifCondition,
        BooleanNot|Equal|Identical $boolExpr,
        FuncCall $funcCall,
        String_ $string,
    ): void
    {
        $scopedString = self::prefixString($prefix, $string);

        $scopedBoolExpr = new BooleanNot(
            self::createNewFuncCall($funcCall, $scopedString),
            $boolExpr->getAttributes(),
        );

        $newCondition = self::createNewCondition($ifCondition, $boolExpr, $scopedBoolExpr);

        $ifStmt->cond = $newCondition;
    }

    private static function createNewCondition(BooleanAnd|BooleanOr|null $previous, BooleanNot|Equal|Identical $left, BooleanNot|Equal|Identical $right): Expr
    {
        $newCondition = new BooleanAnd($left, $right);

        if (null === $previous) {
            return $newCondition;
        }

        $newConditionBinaryOp = clone $previous;

        if ($previous->left === $left) {
            $newConditionBinaryOp->left = $newCondition;
        } else {
            $newConditionBinaryOp->right = $newCondition;
        }

        return $newConditionBinaryOp;
    }

    private static function createNewFuncCall(FuncCall $previous, String_ $string): FuncCall
    {
        $previousArg = $previous->args[0];

        $newFuncCall = clone $previous;
        $newFuncCall->args = [self::createNewArgument($previousArg, $string)];

        return $newFuncCall;
    }

    private static function createNewArgument(Arg $previous, String_ $string): Arg
    {
        $newArg = clone $previous;
        $newArg->value = $string;

        return $newArg;
    }

    private static function prefixString(string $prefix, String_ $previous): String_
    {
        return new String_(
            (string) FullyQualified::concat(
                $prefix,
                $previous->value,
            ),
            $previous->getAttributes(),
        );
    }
}
