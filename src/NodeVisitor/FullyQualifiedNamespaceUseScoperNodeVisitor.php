<?php

namespace Webmozart\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;
use Webmozart\PhpScoper\Util\MutableString;

class FullyQualifiedNamespaceUseScoperNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var MutableString
     */
    private $content;

    /**
     * @var string
     */
    private $prefix;

    public function __construct(MutableString $content, $prefix)
    {
        $this->content = $content;
        $this->prefix = $prefix;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof FullyQualified) {
            $this->content->insert(
                $node->getAttribute('startFilePos'),
                $this->prefix
            );
        }
    }
}
