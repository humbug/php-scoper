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

use Humbug\PhpScoper\Reflector;
use function is_string;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\NodeVisitorAbstract;
use function preg_match;
use TypeError;

/**
 * Prefixes the string scalar values.
 *
 * ```
 * $x = 'Foo\Bar';
 * ```
 *
 * =>
 *
 * ```
 * $x = 'Humbug\Foo\Bar';
 * ```
 */
final class StringScalarPrefixer extends NodeVisitorAbstract
{
    private $prefix;
    private $whitelistedFunctions;
    private $whitelist;
    private $reflector;

    /**
     * @param string    $prefix
     * @param string[]  $whitelistedFunctions
     * @param string[]  $whitelist
     * @param Reflector $reflector
     */
    public function __construct(
        string $prefix,
        array $whitelistedFunctions,
        array $whitelist,
        Reflector $reflector
    ) {
        $this->prefix = $prefix;
        $this->whitelistedFunctions = $whitelistedFunctions;
        $this->whitelist = $whitelist;
        $this->reflector = $reflector;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        return ($this->shouldPrefixScalar($node))
            ? $this->prefixStringScalar($node)
            : $node
        ;
    }

    private function shouldPrefixScalar(Node $node): bool
    {
        if (false === ($node instanceof String_ && AppendParentNode::hasParent($node) && is_string($node->value))
            || 1 !== preg_match('/^\\\\*(?:\p{L}+\\\\+)++\p{L}+$/', $node->value)
        ) {
            return false;
        }
        /** @var String_ $node */
        $parentNode = AppendParentNode::getParent($node);

        if ($parentNode instanceof Arg
            && null !== $funcNode = AppendParentNode::findParent($parentNode)
        ) {
            $funcNode = AppendParentNode::getParent($parentNode);

            return
                $funcNode instanceof FuncCall
                && $funcNode->name instanceof Name
                && false === $funcNode->hasAttribute('whitelist_class_alias')
            ;
        }

        return $parentNode instanceof Assign
            || $parentNode instanceof ArrayItem
            || $parentNode instanceof Param
            || $parentNode instanceof Const_
            || $parentNode instanceof PropertyProperty
        ;
    }

    private function prefixStringScalar(String_ $string): Node
    {
        $stringName = new Name(
            preg_replace('/^\\\\(.+)$/', '$1', $string->value),
            $string->getAttributes()
        );

        // Skip if is already prefixed
        if ($this->prefix === $stringName->getFirst()) {
            $newStringName = $stringName;
        // Check if the class can be prefixed: class from the global namespace
        } elseif (
            $this->reflector->isClassInternal($stringName->toString())
            || 1 === count($stringName->parts)
        ) {
            $newStringName = $stringName;
        } else {
            $newStringName = FullyQualified::concat($this->prefix, $stringName->toString(), $stringName->getAttributes());
        }

        return new String_($newStringName->toString(), $string->getAttributes());
    }
}
