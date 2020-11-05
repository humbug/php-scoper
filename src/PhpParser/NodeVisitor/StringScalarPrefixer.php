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
use Humbug\PhpScoper\Whitelist;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeVisitorAbstract;
use function array_filter;
use function array_key_exists;
use function array_shift;
use function array_values;
use function explode;
use function implode;
use function in_array;
use function is_string;
use function ltrim;
use function preg_match;
use function strpos;
use function strtolower;

/**
 * Prefixes the string scalar values when appropriate.
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
        'class_alias',
        'class_exists',
        'define',
        'defined',
        'function_exists',
        'interface_exists',
        'is_a',
        'is_subclass_of',
        'trait_exists',
    ];

    private const DATETIME_CLASSES = [
        'datetime',
        'datetimeimmutable',
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
        return $node instanceof String_
            ? $this->prefixStringScalar($node)
            : $node
        ;
    }

    private function prefixStringScalar(String_ $string): String_
    {
        if (false === (ParentNodeAppender::hasParent($string) && is_string($string->value))
            || 1 !== preg_match('/^((\\\\)?[\p{L}_\d]+)$|((\\\\)?(?:[\p{L}_\d]+\\\\+)+[\p{L}_\d]+)$/u', $string->value)
        ) {
            return $string;
        }

        $normalizedValue = ltrim($string->value, '\\');

        if ($this->whitelist->belongsToWhitelistedNamespace($string->value)) {
            return $string;
        }

        // From this point either the symbol belongs to the global namespace or the symbol belongs to the symbol
        // namespace is whitelisted

        $parentNode = ParentNodeAppender::getParent($string);

        // The string scalar either has a class form or a simple string which can either be a symbol from the global
        // namespace or a completely unrelated string.

        if ($parentNode instanceof Arg) {
            return $this->prefixStringArg($string, $parentNode, $normalizedValue);
        }

        if ($parentNode instanceof ArrayItem) {
            return $this->prefixArrayItemString($string, $parentNode, $normalizedValue);
        }

        if (false === (
            $parentNode instanceof Assign
                || $parentNode instanceof Param
                || $parentNode instanceof Const_
                || $parentNode instanceof PropertyProperty
                || $parentNode instanceof Return_
        )) {
            return $string;
        }

        // If belongs to the global namespace then we cannot differentiate the value from a symbol and a regular string
        return $this->belongsToTheGlobalNamespace($string)
            ? $string
            : $this->createPrefixedString($string)
        ;
    }

    private function prefixStringArg(String_ $string, Arg $parentNode, string $normalizedValue): String_
    {
        $callerNode = ParentNodeAppender::getParent($parentNode);

        if ($callerNode instanceof New_) {
            return $this->prefixNewStringArg($string, $callerNode);
        }

        if ($callerNode instanceof FuncCall) {
            return $this->prefixFunctionStringArg($string, $callerNode, $normalizedValue);
        }

        if ($callerNode instanceof StaticCall) {
            return $this->prefixStaticCallStringArg($string, $callerNode);
        }

        // If belongs to the global namespace then we cannot differentiate the value from a symbol and a regular
        // string
        return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
    }

    private function prefixNewStringArg(String_ $string, New_ $newNode): String_
    {
        $class = $newNode->class;

        if (false === ($class instanceof FullyQualified)) {
            return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
        }

        if (in_array(strtolower($class->toString()), self::DATETIME_CLASSES, true)) {
            return $string;
        }

        return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
    }

    private function prefixFunctionStringArg(String_ $string, FuncCall $functionNode, string $normalizedValue): String_
    {
        // In the case of a function call, we allow to prefix strings which could be classes belonging to the global
        // namespace in some cases
        $functionName = $functionNode->name instanceof Name ? (string) $functionNode->name : null;

        if (in_array($functionName, ['date_create', 'date', 'gmdate', 'date_create_from_format'], true)) {
            return $string;
        }

        if (false === in_array($functionName, self::SPECIAL_FUNCTION_NAMES, true)) {
            return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
        }

        if ('function_exists' === $functionName) {
            return $this->reflector->isFunctionInternal($normalizedValue)
                ? $string
                : $this->createPrefixedString($string)
            ;
        }

        $isConstantNode = $this->isConstantNode($string);

        if (false === $isConstantNode) {
            if ('define' === $functionName
                && $this->belongsToTheGlobalNamespace($string)
            ) {
                return $string;
            }

            return $this->reflector->isClassInternal($normalizedValue)
                ? $string
                : $this->createPrefixedString($string)
            ;
        }

        return
            (
                $this->whitelist->isSymbolWhitelisted($string->value, true)
                || $this->whitelist->isGlobalWhitelistedConstant($string->value)
                || $this->reflector->isConstantInternal($normalizedValue)
            )
            ? $string
            : $this->createPrefixedString($string)
        ;
    }

    private function prefixStaticCallStringArg(String_ $string, StaticCall $callNode): String_
    {
        $class = $callNode->class;

        if (false === ($class instanceof FullyQualified)) {
            return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
        }

        if (false === in_array(strtolower($class->toString()), self::DATETIME_CLASSES, true)) {
            return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
        }

        if ($callNode->name instanceof Identifier
            && 'createFromFormat' === $callNode->name->toString()
        ) {
            return $string;
        }

        return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
    }

    private function prefixArrayItemString(String_ $string, ArrayItem $parentNode, string $normalizedValue): String_
    {
        // ArrayItem can lead to two results: either the string is used for `spl_autoload_register()`, e.g.
        // `spl_autoload_register(['Swift', 'autoload'])` in which case the string `'Swift'` is guaranteed to be class
        // name, or something else in which case a string like `'Swift'` can be anything and cannot be prefixed.

        $arrayItemNode = $parentNode;

        $parentNode = ParentNodeAppender::getParent($parentNode);

        /** @var Array_ $arrayNode */
        $arrayNode = $parentNode;
        $parentNode = ParentNodeAppender::getParent($parentNode);

        if (false === ($parentNode instanceof Arg)
            || null === $functionNode = ParentNodeAppender::findParent($parentNode)
        ) {
            // If belongs to the global namespace then we cannot differentiate the value from a symbol and a regular string
            return $this->belongsToTheGlobalNamespace($string)
                ? $string
                : $this->createPrefixedString($string)
            ;
        }

        $functionNode = ParentNodeAppender::getParent($parentNode);

        if (false === ($functionNode instanceof FuncCall)) {
            // If belongs to the global namespace then we cannot differentiate the value from a symbol and a regular string
            return $this->belongsToTheGlobalNamespace($string)
                ? $string
                : $this->createPrefixedString($string)
            ;
        }

        /** @var FuncCall $functionNode */
        if (false === ($functionNode->name instanceof Name)) {
            return $string;
        }

        $functionName = (string) $functionNode->name;

        return ('spl_autoload_register' === $functionName
                && array_key_exists(0, $arrayNode->items)
                && $arrayItemNode === $arrayNode->items[0]
                && false === $this->reflector->isClassInternal($normalizedValue)
            )
            ? $this->createPrefixedString($string)
            : $string
        ;
    }

    private function isConstantNode(String_ $node): bool
    {
        $parent = ParentNodeAppender::getParent($node);

        if (false === ($parent instanceof Arg)) {
            return false;
        }

        /** @var Arg $parent */
        $argParent = ParentNodeAppender::getParent($parent);

        if (false === ($argParent instanceof FuncCall)) {
            return false;
        }

        /* @var FuncCall $argParent */
        if (false === ($argParent->name instanceof Name)
            || ('define' !== (string) $argParent->name && 'defined' !== (string) $argParent->name)
        ) {
            return false;
        }

        return $parent === $argParent->args[0];
    }

    private function createPrefixedStringIfDoesNotBelongToGlobalNamespace(String_ $string): String_
    {
        // If belongs to the global namespace then we cannot differentiate the value from a symbol and a regular string
        return $this->belongsToTheGlobalNamespace($string)
            ? $string
            : $this->createPrefixedString($string)
        ;
    }

    private function createPrefixedString(String_ $previous): String_
    {
        $previousValueParts = array_values(
            array_filter(
                explode('\\', $previous->value)
            )
        );

        if ($this->prefix === $previousValueParts[0]) {
            array_shift($previousValueParts);
        }

        $previousValue = implode('\\', $previousValueParts);

        $string = new String_(
            (string) FullyQualified::concat($this->prefix, $previousValue),
            $previous->getAttributes()
        );

        $string->setAttribute(ParentNodeAppender::PARENT_ATTRIBUTE, $string);

        return $string;
    }

    private function belongsToTheGlobalNamespace(String_ $string): bool
    {
        return '' === $string->value || 0 === (int) strpos($string->value, '\\', 1);
    }
}
