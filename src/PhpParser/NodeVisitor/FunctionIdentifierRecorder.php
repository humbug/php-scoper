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

use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\FullyQualifiedNameResolver;
use Humbug\PhpScoper\Whitelist;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeVisitorAbstract;

/**
 * Records the user functions registered in the global namespace which have been whitelisted.
 *
 * @private
 */
final class FunctionIdentifierRecorder extends NodeVisitorAbstract
{
    private $prefix;
    private $nameResolver;
    private $whitelist;

    public function __construct(
        string $prefix,
        FullyQualifiedNameResolver $nameResolver,
        Whitelist $whitelist
    ) {
        $this->prefix = $prefix;
        $this->nameResolver = $nameResolver;
        $this->whitelist = $whitelist;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if (false === ($node instanceof Identifier) || false === ParentNodeAppender::hasParent($node)) {
            return $node;
        }

        $parent = ParentNodeAppender::getParent($node);

        if (false === ($parent instanceof Function_)) {
            return $node;
        }

        /** @var Identifier $node */
        $resolvedName = $this->nameResolver->resolveName($node)->getName();

        if (false === ($resolvedName instanceof FullyQualified)) {
            return $node;
        }

        /** @var FullyQualified $resolvedName */
        if ($this->whitelist->isGlobalWhitelistedFunction((string) $resolvedName)
            || $this->whitelist->isSymbolWhitelisted((string) $resolvedName)
        ) {
            $this->whitelist->recordWhitelistedFunction(
                $resolvedName,
                FullyQualified::concat($this->prefix, $resolvedName)
            );
        }

        return $node;
    }
}
