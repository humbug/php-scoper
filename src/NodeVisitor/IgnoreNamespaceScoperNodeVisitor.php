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

final class IgnoreNamespaceScoperNodeVisitor extends NodeVisitorAbstract
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
        //
        // For use statements
        //
        if ($node instanceof UseUse
            && $node->hasAttribute('parent')
            && false === ($node->getAttribute('parent') instanceof GroupUse)
            && (
                // Is not a whitelisted use statement of the global namespace
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

        /** @var FullyQualified $node */

        // Is a fully qualified call from the global namespace which is not whitelisted
        if (1 === count($node->parts)
            && (false === ($this->whitelister)($node->getFirst()))
        ) {
            $node->setAttribute('phpscoper_ignore', true);

            return $node;
        }

        if (false === $node->hasAttribute('parent')) {
            return $node;
        }

        $parentNode = $node->getAttribute('parent');

        // Is a static method call of a whitelisted class
        if ($parentNode instanceof StaticCall
            && in_array((string) $parentNode->class, $this->whitelist)
        ) {
            $node->setAttribute('phpscoper_ignore', true);

            return $node;
        }

        // Is a method call of a whitelisted class
        if ($parentNode instanceof FuncCall
            && $parentNode->name instanceof Name
            && in_array((string) $parentNode->name->slice(0, -1), $this->whitelist)
        ) {
            $node->setAttribute('phpscoper_ignore', true);

            return $node;
        }

        // Is a new instance of a whitelisted class
        if ($parentNode instanceof New_
            && in_array((string) $parentNode->class->toString(), $this->whitelist)
        ) {
            $node->setAttribute('phpscoper_ignore', true);

            return $node;
        }

        // Is a constant call of a whitelisted class
        if ($parentNode instanceof ClassConstFetch
            && in_array((string) $node, $this->whitelist)
        ) {
            $node->setAttribute('phpscoper_ignore', true);

            return $node;
        }

        return $node;
    }
}
