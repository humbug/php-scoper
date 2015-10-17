<?php

namespace Webmozart\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class FullyQualifiedNamespaceUseScoperNodeVisitor extends NodeVisitorAbstract
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
        if ($node instanceof Node\Name\FullyQualified) {
            return new Node\Name(Node\Name::concat($this->prefix, (string) $node));
        }
    }
}
