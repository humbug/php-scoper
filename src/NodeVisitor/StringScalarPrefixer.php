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
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\NodeVisitorAbstract;

final class StringScalarPrefixer extends NodeVisitorAbstract
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
        if (false === ($node instanceof String_)
            || IgnoreNodeUtility::isNodeIgnored($node)
            || false === AppendParentNode::hasParent($node)
        ) {
            return $node;
        }
        /** @var String_ $node */

        $parentNode = AppendParentNode::getParent($node);

        if (false === ($parentNode instanceof Arg)) {
            return $node;
        }

        $stringName = new Name(
            preg_replace('/^\\\\(.+)$/', '$1', $node->value),
            $node->getAttributes()
        );

        // Skip if is already prefixed
        if ($this->prefix === $stringName->getFirst()) {
            $newStringName = $stringName;
        // Check if the class can be prefixed: class from the global namespace
        } elseif (1 === count($stringName->parts)
            && false === ($this->globalWhitelister)($stringName->toString())
        ) {
            $newStringName = $stringName;
        // Check if the class can be prefixed: regular class
        } elseif (1 < count($stringName->parts)
            && in_array($stringName->toString(), $this->whitelist)
        ) {
            $newStringName = $stringName;
        } else {
            $newStringName = FullyQualified::concat($this->prefix, $stringName->toString(), $stringName->getAttributes());
        }

        return new String_($newStringName->toString(), $node->getAttributes());
    }
}
