<?php

namespace Humbug\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\NodeVisitorAbstract;

final class GroupUseNamespaceScoperNodeVisitor extends NodeVisitorAbstract
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
        if ($node instanceof GroupUse
            && $this->prefix !== $node->prefix->getFirst()
        ) {
            $node->prefix = Name::concat($this->prefix, $node->prefix);
        }

        return $node;
    }
}
