<?php

namespace Humbug\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;

final class NamespaceScoperNodeVisitor extends NodeVisitorAbstract
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
        if ($node instanceof Namespace_ && null !== $node->name) {
            $node->name = Name::concat($this->prefix, $node->name);
        }
    }
}
