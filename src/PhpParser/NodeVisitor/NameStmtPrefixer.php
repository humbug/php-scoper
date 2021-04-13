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
use PhpParser\NodeVisitorAbstract;
use function array_merge;
use function count;
use function in_array;

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
    public const PHP_FUNCTION_KEYWORDS = [
        'self',
        'static',
        'parent',
    ];

    private $prefix;
    private $whitelist;
    private $namespaceStatements;
    private $useStatements;
    private $nameResolver;
    private $reflector;

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

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        return ($node instanceof Name && ParentNodeAppender::hasParent($node))
            ? $this->prefixName($node)
            : $node
        ;
    }

    private function prefixName(Name $name): Node
    {
        $parentNode = ParentNodeAppender::getParent($name);

        if ($parentNode instanceof NullableType) {
            if (false === ParentNodeAppender::hasParent($parentNode)) {
                return $name;
            }

            $parentNode = ParentNodeAppender::getParent($parentNode);
        }

        if (false === (
            $parentNode instanceof ArrowFunction
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
                || $parentNode instanceof Property
                || $parentNode instanceof StaticCall
                || $parentNode instanceof StaticPropertyFetch
        )
        ) {
            return $name;
        }

        if (
            (
                $parentNode instanceof Catch_
                || $parentNode instanceof ClassConstFetch
                || $parentNode instanceof New_
                || $parentNode instanceof FuncCall
                || $parentNode instanceof Instanceof_
                || $parentNode instanceof Param
                || $parentNode instanceof StaticCall
                || $parentNode instanceof StaticPropertyFetch
            )
            && in_array((string) $name, self::PHP_FUNCTION_KEYWORDS, true)
        ) {
            return $name;
        }

        if ($parentNode instanceof ConstFetch && 'null' === (string) $name) {
            return $name;
        }

        $resolvedName = $this->nameResolver->resolveName($name)->getName();

        // Do not prefix if there is a matching use statement.
        $useStatement = $this->useStatements->findStatementForNode($this->namespaceStatements->findNamespaceForNode($name), $name);
        if (
            $useStatement !== null and
            self::array_starts_with($resolvedName->parts, $useStatement->parts) and
            !($parentNode instanceof ConstFetch and ($this->whitelist->isGlobalWhitelistedConstant($resolvedName->toString()) or $this->whitelist->isSymbolWhitelisted($resolvedName->toString(), true))) and
            !($useStatement->getAttribute('parent') and $useStatement->getAttribute('parent')->alias !== null and $this->whitelist->isSymbolWhitelisted($useStatement->toString())) and
            !($name instanceof FullyQualified) and
            $resolvedName->parts !== ['Isolated', 'Symfony', 'Component', 'Finder', 'Finder']
        ) {
            return $name;
        }

        if ($this->prefix === $resolvedName->getFirst() // Skip if is already prefixed
            || $this->whitelist->belongsToWhitelistedNamespace((string) $resolvedName)  // Skip if the namespace node is whitelisted
        ) {
            return $resolvedName;
        }

        // Do not prefix if the Name is inside of the current namespace
        $namespace = $this->namespaceStatements->getCurrentNamespaceName();
        if (
            (
                // In a namespace
                $namespace !== null and
                array_merge($namespace->parts, $name->parts) === $resolvedName->parts
            ) or
            (
                // In the global scope
                $namespace === null and
                $name->parts === $resolvedName->parts and
                !($name instanceof FullyQualified) and
                !($parentNode instanceof ConstFetch) and
                !$this->whitelist->isSymbolWhitelisted($resolvedName->toString()) and
                !$this->reflector->isFunctionInternal($resolvedName->toString()) and
                !$this->reflector->isClassInternal($resolvedName->toString())
            )
        ) {
            return $name;
        }

        // Check if the class can be prefixed
        if (false === ($parentNode instanceof ConstFetch || $parentNode instanceof FuncCall)
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

        // Functions have a fallback autoloading so we cannot prefix them when the name is ambiguous
        // See https://wiki.php.net/rfc/fallback-to-root-scope-deprecation
        if ($parentNode instanceof FuncCall) {
            if ($this->reflector->isFunctionInternal($resolvedName->toString())) {
                return new FullyQualified($resolvedName->toString(), $resolvedName->getAttributes());
            }

            if (false === ($resolvedName instanceof FullyQualified)) {
                return $resolvedName;
            }
        }

        if ($parentNode instanceof ClassMethod && $resolvedName->isSpecialClassName()) {
            return $name;
        }

        return FullyQualifiedFactory::concat(
            $this->prefix,
            $resolvedName->toString(),
            $resolvedName->getAttributes()
        );
    }

    private static function array_starts_with($arr, $prefix): bool
    {
        for ($i = 0; $i < count($prefix); ++$i) {
            if ($arr[$i] !== $prefix[$i]) {
                return false;
            }
        }
        return true;
    }
}
