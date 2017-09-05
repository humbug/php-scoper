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

use Humbug\PhpScoper\NodeVisitor\Resolver\FullyQualifiedNameResolver;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\NodeVisitorAbstract;

final class NameResolver extends NodeVisitorAbstract
{
    private $prefix;
    private $whitelist;
    private $globalWhitelister;
    private $nameResolver;

    /**
     * @param string                     $prefix
     * @param string[]                   $whitelist
     * @param callable                   $globalWhitelister
     * @param FullyQualifiedNameResolver $nameResolver
     */
    public function __construct(
        string $prefix,
        array $whitelist,
        callable $globalWhitelister,
        FullyQualifiedNameResolver $nameResolver
    ) {
        $this->prefix = $prefix;
        $this->whitelist = $whitelist;
        $this->globalWhitelister = $globalWhitelister;
        $this->nameResolver = $nameResolver;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if (false === $node instanceof Name
            || IgnoreNodeUtility::isNodeIgnored($node)
            || false === AppendParentNode::hasParent($node)
        ) {
            return $node;
        }
        /** @var Name $node */

        $parentNode = AppendParentNode::getParent($node);

        if ($parentNode instanceof Namespace_
            || $parentNode instanceof Class_
            || $parentNode instanceof TraitUse
            || $parentNode instanceof Interface_
            || $parentNode instanceof Node\Stmt\UseUse
            || $parentNode instanceof ConstFetch
            || $parentNode instanceof Node\Stmt\TraitUseAdaptation\Precedence
            || $parentNode instanceof Node\Stmt\TraitUseAdaptation\Alias
        ) {
            return $node;
        }

//        if (false === ($parentNode instanceof ClassConstFetch)
//        ) {
//            return $node;
//        }

        $resolvedNode = $this->nameResolver->resolveName($node);

        // Skip if is already prefixed
        if ($this->prefix === $resolvedNode->getFirst()) {
            return $resolvedNode;
        }

        // Check if the class can be prefixed
        if (1 === count($resolvedNode->parts)
            && false === ($this->globalWhitelister)($resolvedNode->toString())
        ) {
            return $resolvedNode;
        } elseif (1 < count($resolvedNode->parts)
            && in_array($resolvedNode->toString(), $this->whitelist)
        ) {
            return $resolvedNode;
        }

        return FullyQualified::concat($this->prefix, $resolvedNode->toString(), $resolvedNode->getAttributes());
    }
}
