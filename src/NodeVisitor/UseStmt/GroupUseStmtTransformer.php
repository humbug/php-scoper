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

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

/**
 * Scopes relevant group statements.
 *
 * ```
 * use Foo{X,Y};
 * ```
 *
 * =>
 *
 * ```
 * use Humbug\Foo{X,Y};
 * ```
 */
final class GroupUseStmtTransformer extends NodeVisitorAbstract
{
    private $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    public function beforeTraverse(array $nodes)
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
                return new Use_(
                    [
                        new UseUse(
                            Name::concat($node->prefix, $use->name, $use->name->getAttributes()),
                            $use->alias,
                            $use->type,
                            $use->getAttributes()
                        )
                    ],
                    $node->type,
                    $node->getAttributes()
                );
            },
            $node->uses
        );
    }
}
