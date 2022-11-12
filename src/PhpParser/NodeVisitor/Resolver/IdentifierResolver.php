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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver;

use Humbug\PhpScoper\PhpParser\Node\NamedIdentifier;
use Humbug\PhpScoper\PhpParser\NodeVisitor\AttributeAppender\ParentNodeAppender;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeVisitor\NameResolver;
use function array_filter;
use function implode;
use function ltrim;

/**
 * Attempts to resolve the identifier node into a fully qualified node. Returns a valid
 * (non fully-qualified) name node on failure.
 *
 * @private
 */
final class IdentifierResolver
{
    public function __construct(private readonly NameResolver $nameResolver)
    {
    }

    public function resolveIdentifier(Identifier $identifier): Name
    {
        $resolvedName = $identifier->getAttribute('resolvedName');

        if (null !== $resolvedName) {
            return $resolvedName;
        }

        $parentNode = ParentNodeAppender::getParent($identifier);

        if ($parentNode instanceof Function_) {
            return $this->resolveFunctionIdentifier($identifier);
        }

        $name = NamedIdentifier::create($identifier);

        return $this->nameResolver
            ->getNameContext()
            ->getResolvedClassName($name);
    }

    public function resolveString(String_ $string): Name
    {
        $name = new FullyQualified(
            ltrim($string->value, '\\'),
            $string->getAttributes(),
        );

        return $this->nameResolver
            ->getNameContext()
            ->getResolvedClassName($name);
    }

    private function resolveFunctionIdentifier(Identifier $identifier): Name
    {
        $nameParts = array_filter([
            $this->nameResolver->getNameContext()->getNamespace(),
            $identifier->toString(),
        ]);

        return new FullyQualified(
            implode(
                '\\',
                $nameParts,
            ),
            $identifier->getAttributes(),
        );
    }
}
