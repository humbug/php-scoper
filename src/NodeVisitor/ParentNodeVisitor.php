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
use PhpParser\NodeVisitorAbstract;

final class ParentNodeVisitor extends NodeVisitorAbstract
{
    private $stack;

    /**
     * @inheritdoc
     */
    public function beforeTraverse(array $nodes)
    {
        $this->stack = [];
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if (!empty($this->stack)) {
            $node->setAttribute('parent', $this->stack[count($this->stack) - 1]);
        }
        $this->stack[] = $node;
    }

    /**
     * @inheritdoc
     */
    public function leaveNode(Node $node)
    {
        array_pop($this->stack);
    }
}
