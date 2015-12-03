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

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Webmozart\PhpScoper\Exception\ParsingException;
use Webmozart\PhpScoper\NodeVisitor\FullyQualifiedNamespaceUseScoperNodeVisitor;
use Webmozart\PhpScoper\NodeVisitor\NamespaceScoperNodeVisitor;
use Webmozart\PhpScoper\NodeVisitor\UseNamespaceScoperNodeVisitor;
use Webmozart\PhpScoper\Util\MutableString;

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
    public function addNamespacePrefix($content, $prefix)
    {
        $mutableContent = new MutableString($content);

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NamespaceScoperNodeVisitor($mutableContent, $prefix));
        $traverser->addVisitor(new UseNamespaceScoperNodeVisitor($mutableContent, $prefix));
        $traverser->addVisitor(new FullyQualifiedNamespaceUseScoperNodeVisitor($mutableContent, $prefix));

        try {
            $statements = $this->parser->parse($content);
        } catch (Error $error) {
            throw new ParsingException($error->getMessage());
        }

        $traverser->traverse($statements);

        return $mutableContent->getModifiedString();
    }
}
