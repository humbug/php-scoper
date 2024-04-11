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

use Humbug\PhpScoper\PhpParser\NodeVisitor\AttributeAppender\ParentNodeAppender;
use Humbug\PhpScoper\PhpParser\UnexpectedParsingScenario;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
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
use function preg_match as native_preg_match;
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
    private const IGNORED_FUNCTIONS = [
        'date',
        'date_create',
        'date_create_from_format',
        'gmdate',
    ];

    // Function for which we know the argument IS a FQCN
    private const SPECIAL_FUNCTION_NAMES = [
        'call_user_func_array',
        'class_alias',
        'class_exists',
        'define',
        'defined',
        'function_exists',
        'interface_exists',
        'is_a',
        'is_callable',
        'is_subclass_of',
        'spl_autoload_register',
        'trait_exists',
    ];

    // Function for which we know the FIRST argument IS a FQCN
    private const SPECIAL_ARRAY_FUNCTION_NAMES = [
        'call_user_func_array',
        'is_callable',
        'spl_autoload_register',
    ];

    private const DATETIME_CLASSES = [
        'datetime',
        'datetimeimmutable',
    ];

    private const CLASS_LIKE_PATTERN = <<<'REGEX'
        /^
            (\\)?               # leading backslash
            (
                [\p{L}_\d]+     # class-like name
                \\              # separator
            )*
            [\p{L}_\d]+         # class-like name
        $/ux
        REGEX;

    private const CONSTANT_FETCH_PATTERN = <<<'REGEX'
        /^
            (\\)?               # leading backslash
            (
                [\p{L}_\d]+     # class-like name
                \\              # separator
            )*
            [\p{L}_\d]+         # class-like name
            ::[\p{L}_\d]+       # constant-like name
        $/ux
        REGEX;

    public function __construct(
        private readonly string                                $prefix,
        private readonly EnrichedReflector                     $enrichedReflector,
        private readonly ExcludedFunctionExistsStringNodeStack $excludedFunctionExistsStringNodeStack,
    ) {
    }

    public function enterNode(Node $node): Node
    {
        return $node instanceof String_
            ? $this->prefixStringScalar($node)
            : $node;
    }

    private function prefixStringScalar(String_ $string): String_
    {
        if (!(ParentNodeAppender::hasParent($string) && is_string($string->value))
            || (
                1 !== native_preg_match(self::CLASS_LIKE_PATTERN, $string->value)
                && 1 !== native_preg_match(self::CONSTANT_FETCH_PATTERN, $string->value)
            )
        ) {
            return $string;
        }

        $normalizedValue = ltrim($string->value, '\\');

        if ($this->enrichedReflector->belongsToExcludedNamespace($string->value)) {
            return $string;
        }

        // From this point either the symbol belongs to the global namespace or
        // the symbol belongs to namespace a non-excluded namespace

        $parentNode = ParentNodeAppender::getParent($string);

        // The string scalar either has a class form or a simple string which
        // can either be a symbol from the global namespace or a completely
        // unrelated string.

        if ($parentNode instanceof Arg) {
            return $this->prefixStringArg($string, $parentNode, $normalizedValue);
        }

        if ($parentNode instanceof ArrayItem) {
            return $this->prefixArrayItemString($string, $parentNode, $normalizedValue);
        }

        if (!(
            $parentNode instanceof Assign
                || $parentNode instanceof Param
                || $parentNode instanceof Const_
                || $parentNode instanceof PropertyProperty
                || $parentNode instanceof Return_
        )) {
            return $string;
        }

        // If belongs to the global namespace then we cannot differentiate the
        // value from a symbol and a regular string hence we leave it alone
        return $this->belongsToTheGlobalNamespace($string)
            ? $string
            : $this->createPrefixedString($string);
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

        if (!($class instanceof Name)) {
            throw UnexpectedParsingScenario::create();
        }

        if (in_array(strtolower($class->toString()), self::DATETIME_CLASSES, true)) {
            return $string;
        }

        return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
    }

    private function prefixFunctionStringArg(String_ $string, FuncCall $functionNode, string $normalizedValue): String_
    {
        // In the case of a function call, we allow prefixing strings which
        // could be classes belonging to the global namespace in some cases
        $functionName = $functionNode->name instanceof Name ? (string) $functionNode->name : null;

        if (in_array($functionName, self::IGNORED_FUNCTIONS, true)) {
            return $string;
        }

        if (!in_array($functionName, self::SPECIAL_FUNCTION_NAMES, true)) {
            return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
        }

        if ('function_exists' === $functionName) {
            if ($this->enrichedReflector->isFunctionExcluded($normalizedValue)) {
                $this->excludedFunctionExistsStringNodeStack->push($string);

                return $string;
            }

            return $this->createPrefixedString($string);
        }

        $isConstantNode = self::isConstantNode($string);

        if (!$isConstantNode) {
            if ('define' === $functionName
                && $this->belongsToTheGlobalNamespace($string)
            ) {
                return $string;
            }

            return $this->enrichedReflector->isClassExcluded($normalizedValue)
                ? $string
                : $this->createPrefixedString($string);
        }

        return $this->enrichedReflector->isExposedConstant($normalizedValue)
            ? $string
            : $this->createPrefixedString($string);
    }

    private function prefixStaticCallStringArg(String_ $string, StaticCall $callNode): String_
    {
        $class = $callNode->class;

        if (!($class instanceof Name)) {
            return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
        }

        if (!in_array(strtolower($class->toString()), self::DATETIME_CLASSES, true)) {
            return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
        }

        if ($callNode->name instanceof Identifier
            && 'createFromFormat' === $callNode->name->toString()
        ) {
            return $string;
        }

        return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
    }

    private function prefixArrayItemString(
        String_ $string,
        ArrayItem $parentNode,
        string $normalizedValue
    ): String_ {
        // ArrayItem can lead to two results: either the string is used for
        // `spl_autoload_register()`, e.g. `spl_autoload_register(['Swift', 'autoload'])`
        // in which case the string `'Swift'` is guaranteed to be class name, or
        // something else in which case a string like `'Swift'` can be anything
        // and cannot be prefixed.
        $arrayItemNode = $parentNode;

        $parentNode = ParentNodeAppender::getParent($parentNode);

        if (!($parentNode instanceof Array_)) {
            return $string;
        }

        $arrayNode = $parentNode;
        $parentNode = ParentNodeAppender::getParent($parentNode);

        if (!($parentNode instanceof Arg)
            || !ParentNodeAppender::hasParent($parentNode)
        ) {
            // If belongs to the global namespace then we cannot differentiate
            // the value from a symbol and a regular string
            return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
        }

        $functionNode = ParentNodeAppender::getParent($parentNode);

        if (!($functionNode instanceof FuncCall)) {
            // If belongs to the global namespace then we cannot differentiate
            // the value from a symbol and a regular string
            return $this->createPrefixedStringIfDoesNotBelongToGlobalNamespace($string);
        }

        if (!($functionNode->name instanceof Name)) {
            return $string;
        }

        $functionName = (string) $functionNode->name;

        return (in_array($functionName, self::SPECIAL_ARRAY_FUNCTION_NAMES, true)
                && array_key_exists(0, $arrayNode->items)
                && $arrayItemNode === $arrayNode->items[0]
                && !$this->enrichedReflector->isClassExcluded($normalizedValue)
        )
            ? $this->createPrefixedString($string)
            : $string;
    }

    private static function isConstantNode(String_ $node): bool
    {
        $parent = ParentNodeAppender::getParent($node);

        if (!($parent instanceof Arg)) {
            throw UnexpectedParsingScenario::create();
        }

        $argParent = ParentNodeAppender::getParent($parent);

        if (!($argParent instanceof FuncCall)) {
            throw UnexpectedParsingScenario::create();
        }

        if (!($argParent->name instanceof Name)
            || !in_array((string) $argParent->name, ['define', 'defined'], true)
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
            : $this->createPrefixedString($string);
    }

    private function belongsToTheGlobalNamespace(String_ $string): bool
    {
        return $this->enrichedReflector->belongsToGlobalNamespace($string->value);
    }

    private function createPrefixedString(String_ $previous): String_
    {
        $previousValueParts = array_values(
            array_filter(
                explode('\\', $previous->value),
            ),
        );

        $previousValueAlreadyPrefixed = $this->prefix === $previousValueParts[0];

        if ($previousValueAlreadyPrefixed) {
            // Remove the prefix and proceed as usual: this ensures that even
            // if the value was correct-ish it is cleaned up (e.g. of leading
            // backslashes)
            array_shift($previousValueParts);
        }

        $previousValue = implode('\\', $previousValueParts);

        $string = new String_(
            (string) FullyQualified::concat($this->prefix, $previousValue),
            $previous->getAttributes(),
        );

        ParentNodeAppender::setParent($string, $string);

        return $string;
    }
}
