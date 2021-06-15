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
use Humbug\PhpScoper\PhpParser\Node\NameFactory;
use Humbug\PhpScoper\PhpParser\NodeVisitor\NamespaceStmt\NamespaceStmtCollection;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\FullyQualifiedNameResolver;
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
use function array_merge;
use function count;
use function in_array;
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
    public const PHP_SPECIAL_KEYWORDS = [
        'self',
        'static',
        'parent',
    ];

    private string $prefix;
    private Whitelist $whitelist;
    private NamespaceStmtCollection $namespaceStatements;
    private UseStmtCollection $useStatements;
    private FullyQualifiedNameResolver $nameResolver;
    private Reflector $reflector;

    public function __construct(
        string $prefix,
        Whitelist $whitelist,
        NamespaceStmtCollection $namespaceStatements,
        UseStmtCollection $useStatements,
        FullyQualifiedNameResolver $nameResolver,
        Reflector $reflector
    ) {
        $this->prefix = $prefix;
        $this->whitelist = $whitelist;
        $this->namespaceStatements = $namespaceStatements;
        $this->useStatements = $useStatements;
        $this->nameResolver = $nameResolver;
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
            return $parent;
        }

        if (!($parent instanceof NullableType)) {
            return $parent;
        }

        return self::findParent($parent);
    }

    private function prefixName(Name $resolvedName, Node $parentNode): Node
    {
        if (false === (
            $parentNode instanceof Alias
            || $parentNode instanceof ArrowFunction
            || $parentNode instanceof Catch_
            || $parentNode instanceof ConstFetch
            || $parentNode instanceof Class_
            || $parentNode instanceof ClassConstFetch
            || $parentNode instanceof ClassMethod
            || $parentNode instanceof FuncCall
            || $parentNode instanceof Function_
            || $parentNode instanceof Instanceof_
            || $parentNode instanceof Interface_
            || $parentNode instanceof New_
            || $parentNode instanceof Param
            || $parentNode instanceof Precedence
            || $parentNode instanceof Property
            || $parentNode instanceof StaticCall
            || $parentNode instanceof StaticPropertyFetch
            || $parentNode instanceof TraitUse
        )
        ) {
            return $resolvedName;
        }

        if (
            (
                $parentNode instanceof Catch_
                || $parentNode instanceof ClassConstFetch
                || $parentNode instanceof New_
                || $parentNode instanceof FuncCall
                || $parentNode instanceof Instanceof_
                || $parentNode instanceof Param
                || $parentNode instanceof Property
                || $parentNode instanceof StaticCall
                || $parentNode instanceof StaticPropertyFetch
            )
            && in_array((string) $resolvedName, self::PHP_SPECIAL_KEYWORDS, true)
        ) {
            return $resolvedName;
        }

        $originalName = OriginalNameResolver::getOriginalName($resolvedName);

        if ($parentNode instanceof ConstFetch && 'null' === $originalName->toLowerString()) {
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

        if ($resolvedName instanceof FullyQualified
            && (
                $this->prefix === $resolvedName->getFirst() // Skip if is already prefixed
                || $this->whitelist->belongsToWhitelistedNamespace((string) $resolvedName)  // Skip if the namespace node is whitelisted
            )
        ) {
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
            || (
                // In the global scope
                $currentNamespace === null
                && $originalName->parts === $resolvedName->parts
                && !($originalName instanceof FullyQualified)
                && !($parentNode instanceof ConstFetch)
                && $resolvedName instanceof FullyQualified
                && !$this->whitelist->isSymbolWhitelisted($resolvedName->toString())
                && !$this->reflector->isFunctionInternal($resolvedName->toString())
                && !$this->reflector->isClassInternal($resolvedName->toString())
            )
        ) {
            return $originalName;
        }

        // Check if the class can be prefixed
        if (!($parentNode instanceof ConstFetch || $parentNode instanceof FuncCall)
            && $resolvedName instanceof FullyQualified
            && $this->reflector->isClassInternal($resolvedName->toString())
        ) {
            return $resolvedName;
        }

        if ($parentNode instanceof ConstFetch) {
            if ($this->whitelist->isSymbolWhitelisted($resolvedName->toString(), true)) {
                return $resolvedName;
            }

            if ($this->reflector->isConstantInternal($resolvedName->toString())) {
                return new FullyQualified($resolvedName->toString(), $resolvedName->getAttributes());
            }

            // Constants have an autoloading fallback so we cannot prefix them when the name is ambiguous
            // See https://wiki.php.net/rfc/fallback-to-root-scope-deprecation
            if (false === ($resolvedName instanceof FullyQualified)) {
                return $resolvedName;
            }

            if ($this->whitelist->isGlobalWhitelistedConstant((string) $resolvedName)) {
                // Unlike classes & functions, whitelisted are not prefixed with aliases registered in scoper-autoload.php
                return new FullyQualified($resolvedName->toString(), $resolvedName->getAttributes());
            }

            // Continue
        }

        // Functions have a fallback auto-loading so we cannot prefix them when the name is ambiguous
        // See https://wiki.php.net/rfc/fallback-to-root-scope-deprecation
        if ($parentNode instanceof FuncCall) {
            if ($this->reflector->isFunctionInternal($originalName->toString())) {
                return new FullyQualified(
                    $originalName->toString(),
                    $originalName->getAttributes(),
                );
            }

            if (!($resolvedName instanceof FullyQualified)) {
                return $resolvedName;
            }
        }

        if ($parentNode instanceof ClassMethod && $resolvedName->isSpecialClassName()) {
            return $resolvedName;
        }

        return FullyQualifiedFactory::concat(
            $this->prefix,
            $resolvedName->toString(),
            $resolvedName->getAttributes()
        );
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

    private static function doesNameBelongToNamespace(
        Name $originalName,
        Name $resolvedName,
        ?Name $namespace
    ): bool
    {
        if (
            $namespace === null
            || !($resolvedName instanceof FullyQualified)
            // In case the original name is a FQ, we do not skip the prefixing
            // and keep it as FQ
            || $originalName instanceof FullyQualified
        ) {
            return false;
        }

        $originalNameFQParts = [
            ...$namespace->parts,
            ...$originalName->parts,
        ];

        return $originalNameFQParts === $resolvedName->parts;
    }

    private static function doesNameBelongToUseStatement(
        Name $originalName,
        Name $resolvedName,
        Node $parentNode,
        ?Name $useStatementName,
        Whitelist $whitelist
    ): bool
    {
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

        if ($parentNode instanceof ConstFetch) {
            // If a constant is whitelisted, it can be that letting a non FQ breaks
            // things. For example the whitelisted namespaced constant could be
            // used via a partial import (in which case it is a regular import not
            // a constant one) which may not be prefixed.
            // For this reason in this scenario we will always transform the
            // constant in a FQ one.
            // Note that this could be adjusted based on the type of the use
            // statement but that requires further changes as at this point we
            // only have the use statement name.
            // TODO: review this statement also check use aliases with constants or functions
            if ($whitelist->isGlobalWhitelistedConstant($resolvedName->toString())
                || $whitelist->isSymbolWhitelisted($resolvedName->toString(), true)
            ) {
                return false;
            }

            return null !== $useStatementName;
        }

        $useStatementParent = ParentNodeAppender::getParent($useStatementName);

        if (!($useStatementParent instanceof UseUse)) {
            return false;
        }

        $useStatementAlias = $useStatementParent->alias;

        if (null === $useStatementAlias) {
            return true;
        }

        // Classes and namespaces usages are case-insensitive
        return in_array(
            $useStatementParent->type,
            [Use_::TYPE_UNKNOWN, Use_::TYPE_NORMAL],
            true
        )
            ? strtolower($originalName->getFirst()) === $useStatementAlias->toLowerString()
            : $originalName->getFirst() === $useStatementAlias->toString();
    }
}
