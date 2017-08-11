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

namespace Humbug\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NodeVisitorAbstract;

/**
 * Scopes the relevant fully qualified nodes.
 */
final class ScopeFullyQualifiedNodeVisitor extends NodeVisitorAbstract
{
    private $prefix;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if ($node instanceof FullyQualified
            && false === (
                $node->hasAttribute('phpscoper_ignore')
                && true === $node->getAttribute('phpscoper_ignore')
            )
        ) {
            return new Name('\\'.Name::concat($this->prefix, (string) $node));
        }

        return $node;
    }
}
