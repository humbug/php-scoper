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
use PhpParser\PrettyPrinter\Standard;
use Webmozart\PhpScoper\Throwable\Exception\ParsingException;
use Webmozart\PhpScoper\NodeVisitor\FullyQualifiedNamespaceUseScoperNodeVisitor;
use Webmozart\PhpScoper\NodeVisitor\NamespaceScoperNodeVisitor;
use Webmozart\PhpScoper\NodeVisitor\UseNamespaceScoperNodeVisitor;

final class Scoper
{
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param string $content Content of the file to scope
     * @param string $prefix Prefix to apply to the file
     *
     * @throws ParsingException
     *
     * @return string Content of the file with the prefix applied
     */
    public function addNamespacePrefix(string $content, string $prefix): string
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NamespaceScoperNodeVisitor($prefix));
        $traverser->addVisitor(new UseNamespaceScoperNodeVisitor($prefix));
        $traverser->addVisitor(new FullyQualifiedNamespaceUseScoperNodeVisitor($prefix));

        try {
            $statements = $this->parser->parse($content);
        } catch (Error $error) {
            throw new ParsingException($error->getMessage(), 0, $error);
        }

        $statements = $traverser->traverse($statements);

        $prettyPrinter = new Standard();

        return $prettyPrinter->prettyPrintFile($statements)."\n";
    }
}
