<?php

namespace Webmozart\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
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
        if ($node instanceof FullyQualified) {
            return new Name(Name::concat($this->prefix, (string) $node));
        }
    }
}
