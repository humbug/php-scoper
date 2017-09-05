<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor;

use PhpParser\Node;

final class IgnoreNodeUtility
{
    /** @private */
    const IGNORE_NODE_ATTRIBUTE = 'phpscoper_ignore';

    public static function isNodeIgnored(Node $node): bool
    {
        return (
            $node->hasAttribute(self::IGNORE_NODE_ATTRIBUTE)
            && true === $node->getAttribute(self::IGNORE_NODE_ATTRIBUTE)
        );
    }

    public static function ignoreNode(Node $node): void
    {
        $node->setAttribute(self::IGNORE_NODE_ATTRIBUTE, true);
    }

    private function __construct()
    {
    }
}