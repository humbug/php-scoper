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

namespace Humbug\PhpScoper\NodeVisitor\UseStmt;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

/**
 * Scopes single level use statements with an alias:
 *
 * ```
 * use Foo as Bar;
 *
 * new Foo();
 * ```
 *
 * =>
 *
 * ```
 * use Humbug\Foo as Bar;
 *
 * new Foo();
 * ```
 */
final class ScopeSingleLevelUseAliasVisitor extends NodeVisitorAbstract
{
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
    public function enterNode(Node $node): Node
    {
        // Collate all single level aliases
        if ($node instanceof UseUse
            && (false === $node->hasAttribute('parent')     // double check if this one may be removed
                || false === ($node->getAttribute('parent') instanceof GroupUse)
            )
            && $this->prefix !== $node->name->getFirst()
            // Is a one level use statement
            && 1 === count($node->name->parts)
            // Has an alias
            && $node->alias !== $node->name->getFirst()
        ) {
            $this->aliases[$node->alias] = $node;

            return $node;
        }

        return $this->scopeUseStmtIfUsedInAnyNameAsAliasedNamespace($node);
    }

    private function scopeUseStmtIfUsedInAnyNameAsAliasedNamespace(Node $node): Node
    {
        if ($node instanceof Name
            && 1 < count($node->parts)
            && in_array($node->getFirst(), array_keys($this->aliases))
        ) {
            $nodeToPrefix = $this->aliases[$node->getFirst()];
            $nodeToPrefix->name = Name::concat($this->prefix, $nodeToPrefix->name);
            unset($this->aliases[$node->getFirst()]);
        }

        return $node;
    }
}
