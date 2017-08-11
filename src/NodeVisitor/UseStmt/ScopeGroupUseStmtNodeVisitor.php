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
final class ScopeGroupUseStmtNodeVisitor extends NodeVisitorAbstract
{
    private $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if ($node instanceof GroupUse
            && $this->prefix !== $node->prefix->getFirst()
        ) {
            $node->prefix = Name::concat($this->prefix, $node->prefix);
        }

        return $node;
    }
}
