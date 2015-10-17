<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\PhpScoper;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use Webmozart\PhpScoper\NodeVisitor\FullyQualifiedNamespaceUseScoperNodeVisitor;
use Webmozart\PhpScoper\NodeVisitor\NamespaceScoperNodeVisitor;
use Webmozart\PhpScoper\NodeVisitor\UseNamespaceScoperNodeVisitor;

class Scoper
{
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param $content
     * @param $prefix
     *
     * @return string
     */
    public function scope($content, $prefix)
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NamespaceScoperNodeVisitor($prefix));
        $traverser->addVisitor(new UseNamespaceScoperNodeVisitor($prefix));
        $traverser->addVisitor(new FullyQualifiedNamespaceUseScoperNodeVisitor($prefix));

        //TODO Manage errors
        $statements = $this->parser->parse($content);
        $statements = $traverser->traverse($statements);

        $prettyPrinter = new Standard();

        return $prettyPrinter->prettyPrintFile($statements)."\n";
    }
}
