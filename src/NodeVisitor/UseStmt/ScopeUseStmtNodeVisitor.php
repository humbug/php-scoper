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

use Humbug\PhpScoper\NodeVisitor\WhitelistedStatements;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

/**
 * Manipulates use statements.
 */
final class ScopeUseStmtNodeVisitor extends NodeVisitorAbstract
{
    private $prefix;
    private $whitelist;
    private $whitelistedStatements;

    /**
     * @param string                $prefix
     * @param string[]              $whitelist
     * @param WhitelistedStatements $whitelistedStatements
     */
    public function __construct(string $prefix, array $whitelist, WhitelistedStatements $whitelistedStatements)
    {
        $this->prefix = $prefix;
        $this->whitelist = $whitelist;
        $this->whitelistedStatements = $whitelistedStatements;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if (false === ($node instanceof UseUse)) {
            return $node;
        }

        /** @var UseUse $node */
        if (Use_::TYPE_UNKNOWN === $node->type) {
            $nodeType = $node->getAttribute('parent')->type;
        } else {
            $nodeType = $node->type;
        }

        // Mark use statements of whitelisted classes
        if (Use_::TYPE_NORMAL === $nodeType && in_array((string) $node->name, $this->whitelist)
        ) {
            $this->whitelistedStatements->addNode($node);

            return $node;
        }

        if ($node->hasAttribute('parent')
            && false === ($node->getAttribute('parent') instanceof GroupUse)
            && $this->prefix !== $node->name->getFirst()
            // Is not an ignored use statement
            && false === (
                $node->hasAttribute('phpscoper_ignore')
                && true === $node->getAttribute('phpscoper_ignore')
            )
        ) {
            $node->name = Name::concat($this->prefix, $node->name);
        }

        return $node;
    }

    /**
     * Removes use statements of whitelisted classes.
     *
     * {@inheritdoc}
     */
    public function leaveNode(Node $node)
    {
        if ($node instanceof Use_ && 0 === count($node->uses)) {
            return NodeTraverser::REMOVE_NODE;
        }

        if (false === ($node instanceof UseUse)) {
            return $node;
        }

        if ($this->whitelistedStatements->has($node)) {
            return NodeTraverser::REMOVE_NODE;
        }

        return $node;
    }

//    /**
//     * @var string
//     */
//    private $prefix;
//
//    /**
//     * @var array
//     */
//    private $aliases;
//
//    public function __construct(string $prefix)
//    {
//        $this->prefix = $prefix;
//    }
//
//    /**
//     * @inheritdoc
//     */
//    public function beforeTraverse(array $nodes)
//    {
//        $this->aliases = [];
//    }
//
//    /**
//     * @inheritdoc
//     */
//    public function enterNode(Node $node)
//    {
//        /* Collate all single level aliases */
//        if ($node instanceof UseUse
//            && (!$node->hasAttribute('parent')
//            || false === ($node->getAttribute('parent') instanceof GroupUse))
//            && $this->prefix !== $node->name->getFirst()
//            && 1 === count($node->name->parts)
//            && $node->alias !== $node->name->getFirst()
//        ) {
//            $this->aliases[$node->alias] = $node;
//
//            return;
//        }
//
//        $this->scopeUseStmtIfUsedInAnyNameAsAliasedNamespace($node);
//    }
//
//    /**
//     * @param Node $node
//     */
//    private function scopeUseStmtIfUsedInAnyNameAsAliasedNamespace(Node $node)
//    {
//        if ($node instanceof Name
//            && 1 < count($node->parts)
//            && in_array($node->getFirst(), array_keys($this->aliases))
//        ) {
//            $nodeToPrefix = $this->aliases[$node->getFirst()];
//            $nodeToPrefix->name = Name::concat($this->prefix, $nodeToPrefix->name);
//            unset($this->aliases[$node->getFirst()]);
//        }
//    }
}
