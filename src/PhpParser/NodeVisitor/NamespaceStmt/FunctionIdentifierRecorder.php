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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor\NamespaceStmt;

use Humbug\PhpScoper\PhpParser\Node\FullyQualifiedFactory;
use Humbug\PhpScoper\PhpParser\NodeVisitor\ParentNodeAppender;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\IdentifierResolver;
use Humbug\PhpScoper\Reflector;
use Humbug\PhpScoper\Whitelist;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeVisitorAbstract;

/**
 * Records the user functions registered in the global namespace which have been whitelisted and whitelisted functions.
 *
 * @private
 */
final class FunctionIdentifierRecorder extends NodeVisitorAbstract
{
    private string $prefix;
    private IdentifierResolver $identifierResolver;
    private Whitelist $whitelist;
    private Reflector $reflector;

    public function __construct(
        string $prefix,
        IdentifierResolver $identifierResolver,
        Whitelist $whitelist,
        Reflector $reflector
    ) {
        $this->prefix = $prefix;
        $this->identifierResolver = $identifierResolver;
        $this->whitelist = $whitelist;
        $this->reflector = $reflector;
    }

    public function enterNode(Node $node): Node
    {
        if (!($node instanceof Identifier || $node instanceof Name || $node instanceof String_)
            || !ParentNodeAppender::hasParent($node)
        ) {
            return $node;
        }

        $resolvedName = $this->retrieveResolvedName($node);

        if (null !== $resolvedName
            && $this->isFunctionWhitelisted($resolvedName)
        ) {
            $this->whitelist->recordWhitelistedFunction(
                $resolvedName,
                FullyQualifiedFactory::concat($this->prefix, $resolvedName),
            );
        }

        return $node;
    }

    private function retrieveResolvedName(Node $node): ?FullyQualified
    {
        if ($node instanceof Identifier) {
            return $this->retrieveResolvedNameForIdentifier($node);
        }

        if ($node instanceof Name) {
            return $this->retrieveResolvedNameForFuncCall($node);
        }

        if ($node instanceof String_) {
            return $this->retrieveResolvedNameForString($node);
        }

        return null;
    }

    private function retrieveResolvedNameForIdentifier(Identifier $identifier): ?FullyQualified
    {
        $parent = ParentNodeAppender::getParent($identifier);

        if (!($parent instanceof Function_)
            || $identifier === $parent->returnType
        ) {
            return null;
        }

        $resolvedName = $this->identifierResolver->resolveIdentifier($identifier);

        return $resolvedName instanceof FullyQualified ? $resolvedName : null;
    }

    private function retrieveResolvedNameForFuncCall(Name $name): ?FullyQualified
    {
        $parent = ParentNodeAppender::getParent($name);

        if (!($parent instanceof FuncCall)) {
            return null;
        }

        return $name instanceof FullyQualified ? $name : null;
    }

    private function retrieveResolvedNameForString(String_ $string): ?FullyQualified
    {
        $stringParent = ParentNodeAppender::getParent($string);

        if (!($stringParent instanceof Arg)) {
            return null;
        }

        $argParent = ParentNodeAppender::getParent($stringParent);

        if (!($argParent instanceof FuncCall)) {
            return null;
        }

        $funcCallName = $argParent->name;

        if (!($funcCallName instanceof FullyQualified)
            || 'function_exists' !== $funcCallName->toString()
        ) {
            return null;
        }

        $resolvedName = $this->identifierResolver->resolveString($string);

        return $resolvedName instanceof FullyQualified ? $resolvedName : null;
    }

    private function isFunctionWhitelisted(FullyQualified $name): bool
    {
        $nameString = (string) $name;

        return (
            !$this->reflector->isFunctionInternal($nameString)
            && (
                $this->whitelist->isGlobalWhitelistedFunction($nameString)
                || $this->whitelist->isSymbolWhitelisted($nameString)
            )
        );
    }
}
