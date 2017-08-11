<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\UseUse;

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