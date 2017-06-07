<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoper;

use Humbug\PhpScoper\NodeVisitor\FullyQualifiedNamespaceUseScoperNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\GroupUseNamespaceScoperNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\IgnoreNamespaceScoperNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\NamespaceScoperNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\ParentNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\UseNamespaceScoperNodeVisitor;
use Humbug\PhpScoper\Throwable\Exception\ParsingException;
use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

/**
 * @final
 */
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
     * @param string $content Content of the file to scope
     * @param string $prefix  Prefix to apply to the file
     *
     * @throws ParsingException
     *
     * @return string Content of the file with the prefix applied
     */
    public function scope(string $content, string $prefix): string
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor(new ParentNodeVisitor());
        $traverser->addVisitor(new GroupUseNamespaceScoperNodeVisitor($prefix));
        $traverser->addVisitor(new IgnoreNamespaceScoperNodeVisitor());
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
