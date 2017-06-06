<?php

declare(strict_types=1);

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
