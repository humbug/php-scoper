<?php

namespace Webmozart\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;
use Webmozart\PhpScoper\Util\MutableString;

class UseNamespaceScoperNodeVisitor extends NodeVisitorAbstract
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
        if ($node instanceof UseUse) {
            $prefix = (0 === strpos($node->name, '\\')) ? $this->prefix : $this->prefix.'\\';

            $this->content->insert(
                $node->getAttribute('startFilePos'),
                $prefix
            );
        }
    }
}
