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

final class WhitelistedStatements
{
    /**
     * @var Node[]
     */
    private $nodes = [];

    public function addNode(Node $node)
    {
        $this->nodes[] = $node;
    }

    public function has(Node $node): bool
    {
        foreach ($this->nodes as $whitelistedNode) {
            if ($node === $whitelistedNode) {
                return true;
            }
        }

        return false;
    }
}
