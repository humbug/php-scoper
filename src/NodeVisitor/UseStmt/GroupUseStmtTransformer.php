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

namespace Humbug\PhpScoper\NodeVisitor\UseStmt;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

/**
 * Transforms the grouped use statements into regular use statements which are easier to work with.
 *
 * ```
 * use A\B\{C\D, function b\c, const D};
 * ```
 *
 * =>
 *
 * ```
 * use Humbug\A\B\C\D;
 * use function Humbug\A\B\b\c;
 * use const Humbug\A\B\D;
 * ```
 *
 * @private
 */
final class GroupUseStmtTransformer extends NodeVisitorAbstract
{
    /**
     * @inheritdoc
     */
    public function beforeTraverse(array $nodes): array
    {
        $newNodes = [];

        foreach ($nodes as $node) {
            if ($node instanceof GroupUse) {
                $uses_ = $this->createUses_($node);

                array_splice($newNodes, count($newNodes), 0, $uses_);
            } else {
                $newNodes[] = $node;
            }
        }

        return $newNodes;
    }

    /**
     * @param GroupUse $node
     *
     * @return Use_[]
     */
    public function createUses_(GroupUse $node): array
    {
        return array_map(
            function (UseUse $use) use ($node): Use_ {
                $newUse = new UseUse(
                    Name::concat($node->prefix, $use->name, $use->name->getAttributes()),
                    $use->alias,
                    $use->type,
                    $use->getAttributes()
                );

                return new Use_(
                    [$newUse],
                    $node->type,
                    $node->getAttributes()
                );
            },
            $node->uses
        );
    }
}
