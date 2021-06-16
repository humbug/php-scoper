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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use function array_pop;
use function count;

/**
 * Appends the parent node as an attribute to each node. This allows to have
 * more context in the other visitors when inspecting a node.
 *
 * @private
 */
final class ParentNodeAppender extends NodeVisitorAbstract
{
    public const PARENT_ATTRIBUTE = 'parent';

    /**
     * @var Node[]
     */
    private array $stack;

    public static function hasParent(Node $node): bool
    {
        return $node->hasAttribute(self::PARENT_ATTRIBUTE);
    }

    public static function getParent(Node $node): Node
    {
        return $node->getAttribute(self::PARENT_ATTRIBUTE);
    }

    public static function findParent(Node $node): ?Node
    {
        return $node->hasAttribute(self::PARENT_ATTRIBUTE)
            ? $node->getAttribute(self::PARENT_ATTRIBUTE)
            : null
        ;
    }

    public function beforeTraverse(array $nodes): ?array
    {
        $this->stack = [];

        return $nodes;
    }

    public function enterNode(Node $node): Node
    {
        if ([] !== $this->stack) {
            $node->setAttribute(self::PARENT_ATTRIBUTE, $this->stack[count($this->stack) - 1]);

            // In some cases, e.g. to replace a node content, we need to access
            // the child nodes early (i.e. before NodeVisitor::enterNode()) in
            // which case without the following they cannot be accessed to
            // with their parent node
            if ($node instanceof Node\Stmt\Const_) {
                foreach ($node->consts as $const) {
                    $const->setAttribute(self::PARENT_ATTRIBUTE, $node);
                    $const->name->setAttribute(self::PARENT_ATTRIBUTE, $const);
                }
            }
        }

        $this->stack[] = $node;

        return $node;
    }

    public function leaveNode(Node $node): Node
    {
        array_pop($this->stack);

        return $node;
    }
}
