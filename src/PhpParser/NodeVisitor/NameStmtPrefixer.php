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

use Humbug\PhpScoper\PhpParser\Node\FullyQualifiedFactory;
use Humbug\PhpScoper\PhpParser\NodeVisitor\NamespaceStmt\NamespaceStmtCollection;
use Humbug\PhpScoper\PhpParser\NodeVisitor\UseStmt\UseStmtCollection;
use Humbug\PhpScoper\Reflector;
use Humbug\PhpScoper\Whitelist;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Catch_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Stmt\TraitUseAdaptation\Alias;
use PhpParser\Node\Stmt\TraitUseAdaptation\Precedence;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;
use UnexpectedValueException;
use function count;
use function get_class;
use function in_array;
use function Safe\sprintf;
use function strtolower;

/**
 * Prefixes names when appropriate.
 *
 * ```
 * new Foo\Bar();
 * ```.
 *
 * =>
 *
 * ```
 * new \Humbug\Foo\Bar();
 * ```
 *
 * @private
 */
final class NameStmtPrefixer extends NodeVisitorAbstract
{
    private const SUPPORTED_PARENT_NODE_CLASS_NAMES = [
        Alias::class,
        ArrowFunction::class,
        Catch_::class,
        ConstFetch::class,
        Class_::class,
        ClassConstFetch::class,
        ClassMethod::class,
        FuncCall::class,
        Function_::class,
        Instanceof_::class,
        Interface_::class,
        New_::class,
        Param::class,
        Precedence::class,
        Property::class,
        StaticCall::class,
        StaticPropertyFetch::class,
        TraitUse::class,
    ];

    private string $prefix;
    private Whitelist $whitelist;
    private NamespaceStmtCollection $namespaceStatements;
    private UseStmtCollection $useStatements;
    private Reflector $reflector;

    public function __construct(
        string $prefix,
        Whitelist $whitelist,
        NamespaceStmtCollection $namespaceStatements,
        UseStmtCollection $useStatements,
        Reflector $reflector
    ) {
        $this->prefix = $prefix;
        $this->whitelist = $whitelist;
        $this->namespaceStatements = $namespaceStatements;
        $this->useStatements = $useStatements;
        $this->reflector = $reflector;
    }

    public function enterNode(Node $node): Node
    {
        if (!($node instanceof Name)) {
            return $node;
        }

        $parent = self::findParent($node);

        return null !== $parent
            ? $this->prefixName($node, $parent)
            : $node;
    }

    private static function findParent(Node $name): ?Node
    {
        $parent = ParentNodeAppender::findParent($name);

        if (null === $parent) {
            return null;
        }

        if (!($parent instanceof NullableType)) {
            return $parent;
        }

        return self::findParent($parent);
    }

    private function prefixName(Name $resolvedName, Node $parentNode): Node
    {
        if (
            $resolvedName->isSpecialClassName()
            || !self::isParentNodeSupported($parentNode)
        ) {
            return $resolvedName;
        }

        $originalName = OriginalNameResolver::getOriginalName($resolvedName);

        if ($parentNode instanceof ConstFetch
            && 'null' === $originalName->toLowerString()
        ) {
            return $originalName;
        }

        // Do not prefix if there is a matching use statement.
        $useStatement = $this->useStatements->findStatementForNode(
            $this->namespaceStatements->findNamespaceForNode($resolvedName),
            $resolvedName,
        );

        if (
            self::doesNameBelongToUseStatement(
                $originalName,
                $resolvedName,
                $parentNode,
                $useStatement,
                $this->whitelist,
            )
        ) {
            return $originalName;
        }

        if ($this->isNamePrefixable($resolvedName)) {
            return $resolvedName;
        }

        // Do not prefix if the Name is inside of the current namespace
        $currentNamespace = $this->namespaceStatements->getCurrentNamespaceName();

        if (
            self::doesNameBelongToNamespace(
                $originalName,
                $resolvedName,
                $currentNamespace,
            )
            || $this->doesNameBelongToGlobalNamespace(
                $originalName,
                $resolvedName,
                $parentNode,
                $currentNamespace,
            )
        ) {
            return $originalName;
        }

        if (!$this->isClassNamePrefixable($resolvedName, $parentNode)) {
            return $resolvedName;
        }

        if ($parentNode instanceof ConstFetch) {
            $prefixedName = $this->prefixConstFetchNode($resolvedName);

            if (null !== $prefixedName) {
                return $prefixedName;
            }

            // Continue
        }

        // Functions have a fallback auto-loading so we cannot prefix them when the name is ambiguous
        // See https://wiki.php.net/rfc/fallback-to-root-scope-deprecation
        if ($parentNode instanceof FuncCall) {
            $prefixedName = $this->prefixFuncCallNode($originalName, $resolvedName);

            if (null !== $prefixedName) {
                return $prefixedName;
            }

            // Continue
        }

        if ($parentNode instanceof ClassMethod
            && $resolvedName->isSpecialClassName()
        ) {
            return $resolvedName;
        }

        return FullyQualifiedFactory::concat(
            $this->prefix,
            $resolvedName->toString(),
            $resolvedName->getAttributes(),
        );
    }

    private static function isParentNodeSupported(Node $parentNode): bool
    {
        foreach (self::SUPPORTED_PARENT_NODE_CLASS_NAMES as $supportedClassName) {
            if ($parentNode instanceof $supportedClassName) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $array
     * @param string[] $start
     */
    private static function arrayStartsWith(array $array, array $start): bool
    {
        $prefixLength = count($start);

        for ($index = 0; $index < $prefixLength; ++$index) {
            if ($array[$index] !== $start[$index]) {
                return false;
            }
        }

        return true;
    }

    private function isNamePrefixable(Name $resolvedName): bool
    {
        if (!($resolvedName instanceof FullyQualified)) {
            return false;
        }

        return (
            // Is already prefixed
            $this->prefix === $resolvedName->getFirst()
            // The namespace node is whitelisted
            || $this->whitelist->belongsToWhitelistedNamespace((string) $resolvedName)
        );
    }

    private static function doesNameBelongToNamespace(
        Name $originalName,
        Name $resolvedName,
        ?Name $namespaceName
    ): bool {
        if (
            $namespaceName === null
            || !($resolvedName instanceof FullyQualified)
            // In case the original name is a FQ, we do not skip the prefixing
            // and keep it as FQ
            || $originalName instanceof FullyQualified
        ) {
            return false;
        }

        $originalNameFQParts = [
            ...$namespaceName->parts,
            ...$originalName->parts,
        ];

        return $originalNameFQParts === $resolvedName->parts;
    }

    private function doesNameBelongToGlobalNamespace(
        Name $originalName,
        Name $resolvedName,
        Node $parentNode,
        ?Name $namespaceName
    ): bool {
        return (
            // In the global scope
            null === $namespaceName
            && !($originalName instanceof FullyQualified)
            && !($parentNode instanceof ConstFetch)
            && !$this->whitelist->isSymbolWhitelisted($resolvedName->toString())
            && !$this->reflector->isFunctionInternal($resolvedName->toString())
            && !$this->reflector->isClassInternal($resolvedName->toString())
        );
    }

    private static function doesNameBelongToUseStatement(
        Name $originalName,
        Name $resolvedName,
        Node $parentNode,
        ?Name $useStatementName,
        Whitelist $whitelist
    ): bool {
        if (
            null === $useStatementName
            || !($resolvedName instanceof FullyQualified)
            // In case the original name is a FQ, we do not skip the prefixing
            // and keep it as FQ
            || $originalName instanceof FullyQualified
            // TODO: review Isolated Finder support
            || $resolvedName->parts === ['Isolated', 'Symfony', 'Component', 'Finder', 'Finder']
            || !self::arrayStartsWith($resolvedName->parts, $useStatementName->parts)
        ) {
            return false;
        }

        [$useStmtAlias, $useStmtType] = self::getUseStmtAliasAndType($useStatementName);

        if ($parentNode instanceof ConstFetch) {
            // If a constant is whitelisted, it can be that letting a non FQ breaks
            // things. For example the whitelisted namespaced constant could be
            // used via a partial import (in which case it is a regular import not
            // a constant one) which may not be prefixed.
            if ($whitelist->isGlobalWhitelistedConstant($resolvedName->toString())
                || $whitelist->isSymbolWhitelisted($resolvedName->toString(), true)
            ) {
                return Use_::TYPE_CONSTANT === $useStmtType;
            }

            return null !== $useStatementName;
        }

        if (null === $useStmtAlias) {
            return true;
        }

        // Classes and namespaces usages are case-insensitive
        $caseSensitiveUseStmt = !in_array(
            $useStmtType,
            [Use_::TYPE_UNKNOWN, Use_::TYPE_NORMAL],
            true,
        );

        return $caseSensitiveUseStmt
            ? $originalName->getFirst() === $useStmtAlias
            : strtolower($originalName->getFirst()) === strtolower($useStmtAlias);
    }

    private function isClassNamePrefixable(
        Name $resolvedName,
        Node $parentNode
    ): bool
    {
        $isClassNode = !($parentNode instanceof ConstFetch || $parentNode instanceof FuncCall);

        return (
            !$isClassNode
            || !($resolvedName instanceof FullyQualified)
            || !$this->reflector->isClassInternal($resolvedName->toString())
        );
    }

    private function prefixConstFetchNode(Name $resolvedName): ?Name
    {
        $resolvedNameString = $resolvedName->toString();

        if ($this->whitelist->isSymbolWhitelisted($resolvedNameString, true)) {
            return $resolvedName;
        }

        if ($this->reflector->isConstantInternal($resolvedNameString)) {
            return new FullyQualified(
                $resolvedNameString,
                $resolvedName->getAttributes(),
            );
        }

        // Constants have an auto-loading fallback so we cannot prefix them when the name is ambiguous
        // See https://wiki.php.net/rfc/fallback-to-root-scope-deprecation
        if (!($resolvedName instanceof FullyQualified)) {
            return $resolvedName;
        }

        if ($this->whitelist->isGlobalWhitelistedConstant($resolvedNameString)) {
            // Unlike classes & functions, whitelisted are not prefixed with aliases registered in scoper-autoload.php
            return new FullyQualified(
                $resolvedNameString,
                $resolvedName->getAttributes(),
            );
        }

        return null;
    }

    private function prefixFuncCallNode(Name $originalName, Name $resolvedName): ?Name
    {
        if ($this->reflector->isFunctionInternal($originalName->toString())) {
            return new FullyQualified(
                $originalName->toString(),
                $originalName->getAttributes(),
            );
        }

        if (!($resolvedName instanceof FullyQualified)) {
            return $resolvedName;
        }

        return null;
    }

    /**
     * @return array{string|null, Use_::TYPE_*}
     */
    private static function getUseStmtAliasAndType(Name $name): array
    {
        $use = ParentNodeAppender::getParent($name);

        if (!($use instanceof UseUse)) {
            throw new UnexpectedValueException(
                sprintf(
                    'Unexpected use statement name parent "%s"',
                    get_class($use),
                ),
            );
        }

        $useParent = ParentNodeAppender::getParent($use);

        if (!($useParent instanceof Use_)) {
            throw new UnexpectedValueException(
                sprintf(
                    'Unexpected UseUse parent "%s"',
                    get_class($useParent),
                ),
            );
        }

        $alias = $use->alias;

        if (null !== $alias) {
            $alias = (string) $alias;
        }

        return [
            $alias,
            $useParent->type,
        ];
    }
}
