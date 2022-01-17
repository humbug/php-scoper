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
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\IdentifierResolver;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;

/**
 * Records the user classes which are exposed.
 *
 * @private
 */
final class ClassIdentifierRecorder extends NodeVisitorAbstract
{
    private string $prefix;
    private IdentifierResolver $identifierResolver;
    private EnrichedReflector $enrichedReflector;
    private SymbolsRegistry $symbolsRegistry;

    public function __construct(
        string $prefix,
        IdentifierResolver $identifierResolver,
        EnrichedReflector $enrichedReflector,
        SymbolsRegistry $symbolsRegistry
    ) {
        $this->prefix = $prefix;
        $this->identifierResolver = $identifierResolver;
        $this->enrichedReflector = $enrichedReflector;
        $this->symbolsRegistry = $symbolsRegistry;
    }

    public function enterNode(Node $node): Node
    {
        if (!($node instanceof Identifier) ||!ParentNodeAppender::hasParent($node)) {
            return $node;
        }

        $parent = ParentNodeAppender::getParent($node);

        if (!($parent instanceof ClassLike) || $parent instanceof Trait_) {
            return $node;
        }

        if (null === $parent->name) {
            return $node;
        }

        $resolvedName = $this->identifierResolver->resolveIdentifier($node);

        if (!($resolvedName instanceof FullyQualified)) {
            return $node;
        }

        if ($this->enrichedReflector->isExposedClass($resolvedName)) {
            $this->symbolsRegistry->recordClass(
                $resolvedName,
                FullyQualifiedFactory::concat($this->prefix, $resolvedName),
            );
        }

        return $node;
    }
}
