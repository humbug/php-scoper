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

use Closure;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;

final class GlobalWhitelistedNamesNodeVisitor extends NodeVisitorAbstract
{
    private $prefix;
    private $whitelister;

    public function __construct(string $prefix, Closure $whitelister)
    {
        $this->prefix = $prefix;
        $this->whitelister = $whitelister;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Name
            && 1 === count($node->parts)
            && true === ($this->whitelister)($node->getFirst())
        ) {
            return Name::concat($this->prefix, $node->getFirst());
        }

        if ($node instanceof Name
            && 1 === count($node->parts)
            && true === ($this->whitelister)($node->getFirst())
        ) {
            return Name::concat($this->prefix, $node->getFirst());
        }

        return $node;
    }
}
