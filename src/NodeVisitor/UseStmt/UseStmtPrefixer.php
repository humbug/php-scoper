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

use Humbug\PhpScoper\NodeVisitor\AppendParentNode;
use Humbug\PhpScoper\NodeVisitor\IgnoreNodeUtility;
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
final class UseStmtPrefixer extends NodeVisitorAbstract
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
            $nodeType = AppendParentNode::getParent($node)->type;
        } else {
            $nodeType = $node->type;
        }

        // Mark use statements of whitelisted classes
        if (Use_::TYPE_NORMAL === $nodeType && in_array((string) $node->name, $this->whitelist)
        ) {
            $this->whitelistedStatements->addNode($node);

            return $node;
        }

        if (AppendParentNode::hasParent($node)
            && false === (AppendParentNode::getParent($node) instanceof GroupUse)
            // The prefix is not already applied
            && $this->prefix !== $node->name->getFirst()
            // Is not an ignored use statement
            && false === IgnoreNodeUtility::isNodeIgnored($node)
        ) {
            $node->name = Name::concat($this->prefix, $node->name);
        }

        return $node;
    }
//
//    /**
//     * Removes use statements of whitelisted classes.
//     *
//     * {@inheritdoc}
//     */
//    public function leaveNode(Node $node)
//    {
//        if ($node instanceof Use_ && 0 === count($node->uses)) {
//            return NodeTraverser::REMOVE_NODE;
//        }
//
//        if (false === ($node instanceof UseUse)) {
//            return $node;
//        }
//
//        if ($this->whitelistedStatements->has($node)) {
//            return NodeTraverser::REMOVE_NODE;
//        }
//
//        return $node;
//    }
}
