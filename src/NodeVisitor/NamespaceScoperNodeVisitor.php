<?php

namespace Webmozart\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeVisitorAbstract;
use Webmozart\PhpScoper\Util\MutableString;

class NamespaceScoperNodeVisitor extends NodeVisitorAbstract
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
        if ($node instanceof Namespace_ && null !== $node->name) {
            $prefix = (0 === strpos($node->name, '\\')) ? $this->prefix : $this->prefix.'\\';

            $this->content->insert(
                $node->getAttribute('startFilePos') + strlen('namespace '),
                $prefix
            );
        }
    }
}
