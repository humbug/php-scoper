<?php

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeVisitorAbstract;
use function array_map;
use function count;

/**
 * Replaces multi-constants declarations into multiple single-constant
 * declarations.
 * This is to allow ConstStmtReplacer to do its job without having to worry
 * about this multi-constant declaration case which it cannot handle.
 *
 * ```
 * const FOO = 'foo', BAR = 'bar';
 * ```
 *
 * =>
 *
 * ```
 * const FOO = 'foo';
 * const BAR = 'bar';
 * ```
 *
 * @private
 */
final class MultiConstStmtReplacer extends NodeVisitorAbstract
{
    public function enterNode(Node $node): Node
    {
        if (!$node instanceof Const_) {
            return $node;
        }

        if (count($node->consts) <= 1) {
            return $node;
        }

        $newStatements = array_map(
            static function (Node\Const_ $const) use ($node): Const_ {
                $newConstNode = clone $node;
                $newConstNode->consts = [$const];

                return $newConstNode;
            },
            $node->consts,
        );

        // Workaround to replace the statement.
        // See https://github.com/nikic/PHP-Parser/issues/507
        return new If_(
            new ConstFetch(new Name('true')),
            ['stmts' => $newStatements]
        );
    }
}
