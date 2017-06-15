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

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\NodeVisitor\FullyQualifiedNamespaceUseScoperNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\GroupUseNamespaceScoperNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\IgnoreNamespaceScoperNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\NamespaceScoperNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\ParentNodeVisitor;
use Humbug\PhpScoper\NodeVisitor\UseNamespaceScoperNodeVisitor;
use Humbug\PhpScoper\Scoper;
use PhpParser\Error as PhpParserError;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

final class PhpScoper implements Scoper
{
    /** @internal */
    const FILE_PATTERN = '/.*\.php$/';

    private $parser;
    private $decoratedScoper;

    public function __construct(Parser $parser, Scoper $decoratedScoper)
    {
        $this->parser = $parser;
        $this->decoratedScoper = $decoratedScoper;
    }

    /**
     * Scopes PHP files.
     *
     * {@inheritdoc}
     *
     * @throws PhpParserError
     */
    public function scope(string $filePath, string $prefix): string
    {
        if (1 !== preg_match(self::FILE_PATTERN, $filePath)) {
            return $this->decoratedScoper->scope($filePath, $prefix);
        }

        $content = file_get_contents($filePath);

        $traverser = $this->createTraverser($prefix);

        $statements = $this->parser->parse($content);
        $statements = $traverser->traverse($statements);

        $prettyPrinter = new Standard();

        return $prettyPrinter->prettyPrintFile($statements)."\n";
    }

    private function createTraverser(string $prefix): NodeTraverserInterface
    {
        $traverser = new NodeTraverser();

        $traverser->addVisitor(new ParentNodeVisitor());
        $traverser->addVisitor(new IgnoreNamespaceScoperNodeVisitor());
        $traverser->addVisitor(new GroupUseNamespaceScoperNodeVisitor($prefix));
        $traverser->addVisitor(new NamespaceScoperNodeVisitor($prefix));
        $traverser->addVisitor(new UseNamespaceScoperNodeVisitor($prefix));
        $traverser->addVisitor(new FullyQualifiedNamespaceUseScoperNodeVisitor($prefix));

        return $traverser;
    }
}
