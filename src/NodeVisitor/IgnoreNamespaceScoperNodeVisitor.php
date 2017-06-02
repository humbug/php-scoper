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

namespace Webmozart\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

class IgnoreNamespaceScoperNodeVisitor extends NodeVisitorAbstract
{

    private static $reserved = [
        'Closure'
    ];

    public function enterNode(Node $node)
    {
        if ($node instanceof UseUse && in_array((string) $node->name, self::$reserved)) {
            $node->setAttribute('phpscoper_ignore', true);
        }
    }
}
