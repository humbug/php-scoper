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

use function array_key_exists;
use function count;
use function explode;
use function Humbug\PhpScoper\is_stringable;
use Humbug\PhpScoper\Reflector;
use Humbug\PhpScoper\Whitelist;
use function in_array;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Array_;
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
use function strpos;
use Throwable;

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
    private const SPECIAL_FUNCTION_NAMES = [
        'is_a',
        'is_subclass_of',
        'interface_exists',
        'class_exists',
        'trait_exists',
        'function_exists',
        'class_alias',
    ];

    private $prefix;
    private $whitelist;
    private $reflector;

    public function __construct(string $prefix, Whitelist $whitelist, Reflector $reflector)
    {
        $this->prefix = $prefix;
        $this->whitelist = $whitelist;
        $this->reflector = $reflector;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        $isSpecialFunction = false;

        return ($this->shouldPrefixScalar($node, $isSpecialFunction))
            ? $this->prefixStringScalar($node, $isSpecialFunction)
            : $node
        ;
    }

    private function shouldPrefixScalar(Node $node, bool &$isSpecialFunction): bool
    {
        if (false === ($node instanceof String_ && AppendParentNode::hasParent($node) && is_string($node->value))
            || 1 !== preg_match('/^((\\\\)?[\p{L}_]+)|((\\\\)?(?:[\p{L}_]+\\\\+)+[\p{L}_]+)$/u', $node->value)
        ) {
            return false;
        }

        /** @var String_ $node */
        $parentNode = AppendParentNode::getParent($node);

        // The string scalar either has a class form or a simple string which can either be a symbol from the global
        // namespace or a completely unrelated string.

        if ($parentNode instanceof Arg
            && null !== $functionNode = AppendParentNode::findParent($parentNode)
        ) {
            $functionNode = AppendParentNode::getParent($parentNode);

            if ($functionNode instanceof FuncCall) {
                $functionName = is_stringable($functionNode->name) ? (string) $functionNode->name : null;

                if (false === strpos((string) $node->value, '\\')
                    && null !== $functionName
                    && in_array($functionName, self::SPECIAL_FUNCTION_NAMES, true)
                ) {
                    $isSpecialFunction = true;

                    return (
                        (
                            'function_exists' === $functionName
                            && false === $this->reflector->isFunctionInternal($node->value)
                        )
                        || (
                            'function_exists' !== $functionName
                            && false === $this->reflector->isClassInternal($node->value)
                            && false === $this->whitelist->isClassWhitelisted($node->value)
                        )
                    );
                }

                return $functionNode->name instanceof Name && false === $functionNode->hasAttribute('whitelist_class_alias');
            }

            return $functionNode instanceof MethodCall || $functionNode instanceof StaticCall;
        }

        if (false === ($parentNode instanceof ArrayItem)) {
            return $parentNode instanceof Assign
                || $parentNode instanceof Param
                || $parentNode instanceof Const_
                || $parentNode instanceof PropertyProperty
            ;
        }

        // ArrayItem can lead to two results: either the string is used for `spl_autoload_register()`, e.g.
        // `spl_autoload_register(['Swift', 'autoload'])` in which case the string `'Swift'` is guaranteed to be class
        // name, or something else in which case a string like `'Swift'` can be anything and cannot be prefixed.

        if (substr_count($node->value, '\\') + 1 > 1) {
            return true;
        }

        $arrayItemNode = $parentNode;

        if (false === AppendParentNode::hasParent($parentNode)) {
            return false;
        }

        $parentNode = AppendParentNode::getParent($parentNode);

        if (false === ($parentNode instanceof Array_) || false === AppendParentNode::hasParent($parentNode)) {
            return false;
        }

        /** @var Array_ $arrayNode */
        $arrayNode = $parentNode;
        $parentNode = AppendParentNode::getParent($parentNode);

        if (false === ($parentNode instanceof Arg)
            || null === $functionNode = AppendParentNode::findParent($parentNode)
        ) {
            return false;
        }

        $functionNode = AppendParentNode::getParent($parentNode);

        if (false === ($functionNode instanceof FuncCall)) {
            return false;
        }

        /** @var FuncCall $functionNode */

        if (is_stringable($functionNode->name)) {
            $functionName = (string) $functionNode->name;
        } else {
            return false;
        }

        if ('spl_autoload_register' === $functionName
            && array_key_exists(0, $arrayNode->items)
            && $arrayItemNode === $arrayNode->items[0]
        ) {
            $isSpecialFunction = true;

            return (
                false === $this->whitelist->isClassWhitelisted($node->value)
                && false === $this->reflector->isClassInternal($node->value)
            );
        }

        return false;
    }

    private function prefixStringScalar(String_ $string, bool $isSpecialFunction): Node
    {
        $stringName = new Name(
            preg_replace('/^\\\\(.+)$/', '$1', $string->value),
            $string->getAttributes()
        );

        $isConstantNode = $this->isConstantNode($string);

        // Skip if is already prefixed
        if ($this->prefix === $stringName->getFirst()) {
            $newStringName = $stringName;
        } elseif ($isSpecialFunction) {
            $newStringName = FullyQualified::concat($this->prefix, $stringName->toString(), $stringName->getAttributes());
        // Check if the class can be prefixed: class not from the global namespace or which the namespace is not
        // whitelisted
        } elseif (
            1 === count($stringName->parts)
            || $this->reflector->isClassInternal($stringName->toString())
            || (false === $isConstantNode && $this->whitelist->isClassWhitelisted((string) $stringName))
            || ($isConstantNode && $this->whitelist->isConstantWhitelisted((string) $stringName))
            || $this->whitelist->isNamespaceWhitelisted((string) $stringName)
        ) {
            $newStringName = $stringName;
        } else {
            $newStringName = FullyQualified::concat($this->prefix, $stringName->toString(), $stringName->getAttributes());
        }

        return new String_($newStringName->toString(), $string->getAttributes());
    }

    private function isConstantNode(String_ $node): bool
    {
        $parent = AppendParentNode::getParent($node);

        if (false === ($parent instanceof Arg)) {
            return false;
        }

        /** @var Arg $parent */
        $argParent = AppendParentNode::getParent($parent);

        if (false === ($argParent instanceof FuncCall)) {
            return false;
        }

        /* @var FuncCall $argParent */
        return 'define' === (string) $argParent->name;
    }
}
