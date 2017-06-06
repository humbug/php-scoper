<?php

namespace Humbug\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

final class UseNamespaceScoperNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $prefix;

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof UseUse
            && $node->hasAttribute('parent') && false === ($node->getAttribute('parent') instanceof  GroupUse)
            && $this->prefix !== $node->name->getFirst()
        ) {
            $node->name = Name::concat($this->prefix, $node->name);
        }

        return $node;
    }
}
