<?php

namespace Webmozart\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

class UseNamespaceScoperNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $prefix;

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof UseUse) {
            if ($node->hasAttribute('phpscoper_ignore')) {
                return;
            }
            $node->name = Name::concat($this->prefix, $node->name);
        }
    }
}
