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

use Humbug\PhpScoper\Reflector;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\NodeVisitorAbstract;
use function is_string;
use function preg_match;

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
 *
 * @private
 */
final class StringScalarPrefixer extends NodeVisitorAbstract
{
    private $prefix;
    private $reflector;

    public function __construct(string $prefix, Reflector $reflector)
    {
        $this->prefix = $prefix;
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
            || 1 !== preg_match('/^\\\\*(?:[\p{L}_]+\\\\+)++[\p{L}_]+$/u', $node->value)
        ) {
            return false;
        }
        /** @var String_ $node */
        $parentNode = AppendParentNode::getParent($node);

        if ($parentNode instanceof Arg
            && null !== $funcNode = AppendParentNode::findParent($parentNode)
        ) {
            $funcNode = AppendParentNode::getParent($parentNode);

            if ($funcNode instanceof FuncCall) {
                return $funcNode->name instanceof Name && false === $funcNode->hasAttribute('whitelist_class_alias');
            }

            return $funcNode instanceof MethodCall || $funcNode instanceof StaticCall;
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
            1 === count($stringName->parts)
            || $this->reflector->isClassInternal($stringName->toString())
        ) {
            $newStringName = $stringName;
        } else {
            $newStringName = FullyQualified::concat($this->prefix, $stringName->toString(), $stringName->getAttributes());
        }

        return new String_($newStringName->toString(), $string->getAttributes());
    }
}
