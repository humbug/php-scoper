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
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

final class SingleLevelUseAliasVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var array
     */
    private $aliases;

    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @inheritdoc
     */
    public function beforeTraverse(array $nodes)
    {
        $this->aliases = [];
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        /* Collate all single level aliases */
        if ($node instanceof UseUse
            && (!$node->hasAttribute('parent')
            || false === ($node->getAttribute('parent') instanceof GroupUse))
            && $this->prefix !== $node->name->getFirst()
            && 1 === count($node->name->parts)
            && $node->alias !== $node->name->getFirst()
        ) {
            $this->aliases[$node->alias] = $node;

            return;
        }

        $this->scopeUseStmtIfUsedInAnyNameAsAliasedNamespace($node);
    }

    /**
     * @param Node $node
     */
    private function scopeUseStmtIfUsedInAnyNameAsAliasedNamespace(Node $node)
    {
        if ($node instanceof Name
            && 1 < count($node->parts)
            && in_array($node->getFirst(), array_keys($this->aliases))
        ) {
            $nodeToPrefix = $this->aliases[$node->getFirst()];
            $nodeToPrefix->name = Name::concat($this->prefix, $nodeToPrefix->name);
            unset($this->aliases[$node->getFirst()]);
        }
    }

}
