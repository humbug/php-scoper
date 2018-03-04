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

use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\FullyQualifiedNameResolver;
use Humbug\PhpScoper\Reflector;
use PhpParser\Node;
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
use PhpParser\NodeVisitorAbstract;
use function in_array;

/**
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
    private $nameResolver;
    private $reflector;

    /**
     * @param string                     $prefix
     * @param FullyQualifiedNameResolver $nameResolver
     * @param Reflector                  $reflector
     */
    public function __construct(
        string $prefix,
        FullyQualifiedNameResolver $nameResolver,
        Reflector $reflector
    ) {
        $this->prefix = $prefix;
        $this->nameResolver = $nameResolver;
        $this->reflector = $reflector;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        return ($node instanceof Name && AppendParentNode::hasParent($node))
            ? $this->prefixName($node)
            : $node
        ;
    }

    private function prefixName(Name $name): Node
    {
        $parentNode = AppendParentNode::getParent($name);

        if ($parentNode instanceof NullableType) {
            if (false === AppendParentNode::hasParent($parentNode)) {
                return $name;
            }

            $parentNode = AppendParentNode::getParent($parentNode);
        }

        if (false === (
                $parentNode instanceof ConstFetch
                || $parentNode instanceof ClassConstFetch
                || $parentNode instanceof StaticPropertyFetch
                || $parentNode instanceof Param
                || $parentNode instanceof FuncCall
                || $parentNode instanceof StaticCall
                || $parentNode instanceof Function_
                || $parentNode instanceof ClassMethod
                || $parentNode instanceof New_
                || $parentNode instanceof Class_
                || $parentNode instanceof Interface_
                || $parentNode instanceof Catch_
                || $parentNode instanceof Instanceof_
            )
        ) {
            return $name;
        }

        if (
            (
                $parentNode instanceof FuncCall
                || $parentNode instanceof StaticCall
                || $parentNode instanceof ClassConstFetch
                || $parentNode instanceof StaticPropertyFetch
                || $parentNode instanceof New_
                || $parentNode instanceof Param
                || $parentNode instanceof Catch_
                || $parentNode instanceof Instanceof_
            )
            && in_array((string) $name, self::PHP_FUNCTION_KEYWORDS, true)
        ) {
            return $name;
        }

        if ($parentNode instanceof ConstFetch && 'null' === (string) $name) {
            return $name;
        }

        $resolvedValue = $this->nameResolver->resolveName($name);

        $resolvedName = $resolvedValue->getName();

        // Skip if is already prefixed
        if ($this->prefix === $resolvedName->getFirst()) {
            return $resolvedName;
        }

        // Check if the class can be prefixed
        if (false === ($parentNode instanceof ConstFetch || $parentNode instanceof FuncCall)) {
            if ($this->reflector->isClassInternal($resolvedName->toString())) {
                return $resolvedName;
            }
        }

        // Constants have a fallback autoloading so we cannot prefix them when the name is ambiguous
        // See https://wiki.php.net/rfc/fallback-to-root-scope-deprecation
        if ($parentNode instanceof ConstFetch) {
            if ($this->reflector->isConstantInternal($resolvedName->toString())) {
                return new FullyQualified($resolvedName->toString(), $resolvedName->getAttributes());
            }

            if (false === ($resolvedName instanceof FullyQualified)) {
                return $resolvedName;
            }
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

        if ('self' === (string) $resolvedName && $parentNode instanceof ClassMethod) {
            return $name;
        }

        return FullyQualified::concat(
            $this->prefix,
            $resolvedName->toString(),
            $resolvedName->getAttributes()
        );
    }
}
