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

namespace Webmozart\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\NodeVisitorAbstract;

class GroupUseNamespaceScoperNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @param string $prefix
     */
    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param Node[] $nodes
     */
    public function beforeTraverse(array $nodes)
    {
        foreach ($nodes as $node) {
            if ($node instanceof GroupUse) {
                $this->ignoreUseUses($node);
            }
        }
    }

    /**
     * @param Node $node
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof GroupUse) {
            $node->prefix = Name::concat($this->prefix, $node->prefix);
        }
    }

    /**
     * @param GroupUse $node
     */
    private function ignoreUseUses(GroupUse $node)
    {
        foreach ($node->uses as $use) {
            $use->setAttribute('phpscoper_ignore', true);
        }
    }
}
