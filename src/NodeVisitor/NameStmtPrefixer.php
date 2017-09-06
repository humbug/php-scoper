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
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\NodeVisitorAbstract;

final class NameStmtPrefixer extends NodeVisitorAbstract
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
        if (false === ($node instanceof Name)
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
            || $parentNode instanceof Node\Stmt\TraitUseAdaptation\Precedence
            || $parentNode instanceof Node\Stmt\TraitUseAdaptation\Alias
        ) {
            return $node;
        }

//        if (false === ($parentNode instanceof ClassConstFetch)
//        ) {
//            return $node;
//        }

        $resolvedValue = $this->nameResolver->resolveName($node);

        $resolvedName = $resolvedValue->getName();

        // Skip if is already prefixed
        if ($this->prefix === $resolvedName->getFirst()) {
            return $resolvedName;
        }

        // Check if the class can be prefixed
        if (false === ($parentNode instanceof ConstFetch || $parentNode instanceof FuncCall)) {
            if (1 === count($resolvedName->parts)
                && false === ($this->globalWhitelister)($resolvedName->toString())
            ) {
                return $resolvedName;
            } elseif (1 < count($resolvedName->parts)
                && in_array($resolvedName->toString(), $this->whitelist)
            ) {
                return $resolvedName;
            }
        }

        // Can we get rid of all the ignore? Seems unnecessary

        if ($parentNode instanceof ConstFetch
            && 1 === count($resolvedName->parts)
            && null === $resolvedValue->getUse()
        ) {
            return $resolvedName;
        }

        if ($parentNode instanceof FuncCall
            && 1 === count($resolvedName->parts)
            && null === $resolvedValue->getUse()
        ) {
            return $resolvedName;
        }

        return FullyQualified::concat($this->prefix, $resolvedName->toString(), $resolvedName->getAttributes());
    }
}
