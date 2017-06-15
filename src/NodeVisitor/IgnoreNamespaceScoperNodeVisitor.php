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

namespace Humbug\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

final class IgnoreNamespaceScoperNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof FullyQualified
            && $node->isFullyQualified()
            && 1 === count($node->parts)
        ) {
            $node->setAttribute('phpscoper_ignore', true);
        }

        if ($node instanceof UseUse
            && $node->hasAttribute('parent')
            && false === ($node->getAttribute('parent') instanceof GroupUse)
            && (1 === count($node->name->parts) || 'Composer' === $node->name->getFirst())
        ) {
            $node->setAttribute('phpscoper_ignore', true);
        }
    }
}
