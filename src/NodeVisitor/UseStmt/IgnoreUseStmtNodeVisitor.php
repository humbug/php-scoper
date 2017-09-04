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
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

/**
 * Whitelists from the scoping the relevant use statements:.
 *
 * ```
 * use Closure;
 * use Foo;
 * use Composer\Composer;
 * ```
 */
final class IgnoreUseStmtNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if ($node instanceof UseUse
            && $node->hasAttribute('parent')
            && false === ($node->getAttribute('parent') instanceof GroupUse)
            // If is a single level use statements or part of the Composer namespace
            && (1 === count($node->name->parts) || 'Composer' === $node->name->getFirst())
        ) {
            $node->setAttribute('phpscoper_ignore', true);
        }

        return $node;
    }
}
