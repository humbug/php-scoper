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
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeVisitorAbstract;
use function count;

/**
 * Records the user functions registered in the global namespace.
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
        if (false === ($node instanceof Identifier) || false === AppendParentNode::hasParent($node)) {
            return $node;
        }

        $parent = AppendParentNode::getParent($node);

        if (false === ($parent instanceof Function_)) {
            return $node;
        }

        /** @var Identifier $node */
        $resolvedValue = $this->nameResolver->resolveName(
            new Name(
                $node->name,
                $node->getAttributes()
            )
        );
        $resolvedName = $resolvedValue->getName();

        if (null !== $resolvedValue->getNamespace()
            || false === ($resolvedName instanceof FullyQualified)
            || count($resolvedName->parts) > 1
        ) {
            return $node;
        }

        /* @var FullyQualified $resolvedName */

        $this->whitelist->recordUserGlobalFunction(
            $resolvedName,
            FullyQualified::concat($this->prefix, $resolvedName)
        );

        return $node;
    }
}
