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

namespace Humbug\PhpScoper\NodeVisitor\Ignore;

use Humbug\PhpScoper\NodeVisitor\AppendParentNode;
use Humbug\PhpScoper\NodeVisitor\IgnoreNodeUtility;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

final class UseIgnoreNodeVisitor extends NodeVisitorAbstract
{
    private $whitelist;
    private $whitelister;

    /**
     * @param string[] $whitelist
     * @param callable $whitelister
     */
    public function __construct(array $whitelist, callable $whitelister)
    {
        $this->whitelist = $whitelist;
        $this->whitelister = $whitelister;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if (false === ($node instanceof UseUse) || false === AppendParentNode::hasParent($node)) {
            return $node;
        }

        $parentNode = AppendParentNode::getParent($node);


        // The parent node should be prefixed no the current node
        // $parentNode instanceof GroupUse
        if (
            $this->isGlobalNotWhitelisted($node, $parentNode)
            // Is not from the Composer namespace
            || 'Composer' === $node->name->getFirst()
            || $this->isWhitelisted($node, $parentNode)
        ) {
            IgnoreNodeUtility::ignoreNode($node);

            return $node;
        }

        return $node;
        $x = ';';
        //
        // For use statements
        //
        if ($node instanceof UseUse
            && AppendParentNode::hasParent($node)
            && false === (AppendParentNode::getParent($node) instanceof GroupUse)
            && (
                // Is a whitelisted use statement of the global namespace
                (1 === count($node->name->parts) && false === ($this->whitelister)($node->name->getFirst()))
                // Is not from the Composer namespace
                || 'Composer' === $node->name->getFirst()
                // Is not a whitelisted class
                || ($node->getAttribute('parent') instanceof Use_
                    && Use_::TYPE_NORMAL === $node->getAttribute('parent')->type
                    && in_array((string) $node->name, $this->whitelist)
                )
            )
        ) {
            $node->setAttribute('phpscoper_ignore', true);

            return $node;
        } elseif (false === ($node instanceof FullyQualified) || false === $node->isFullyQualified()) {
            return $node;
        }

        return $node;
    }

    private function isGlobalNotWhitelisted(UseUse $node, Use_ $parentNode): bool
    {
        return (
            $parentNode->type === Use_::TYPE_NORMAL
            && 1 === count($node->name->parts)  // Is from the global namespace
            && false === ($this->whitelister)($node->name->getFirst())    // Is not (global) whitelisted
        );
    }

    private function isWhitelisted(UseUse $node, Use_ $parentNode): bool
    {
        return (
            $parentNode->type === Use_::TYPE_NORMAL
            && 1 !== count($node->name->parts)  // Is not from the global namespace
            && in_array((string) $node->name, $this->whitelist)    // Is whitelisted
        );
    }
}
