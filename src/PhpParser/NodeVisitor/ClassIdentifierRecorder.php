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
use Humbug\PhpScoper\PhpParser\UnexpectedParsingScenario;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\NodeVisitorAbstract;

/**
 *  Records the classes that need to be aliased.
 *
 * @private
 */
final class ClassIdentifierRecorder extends NodeVisitorAbstract
{
    public function __construct(
        private readonly string $prefix,
        private readonly IdentifierResolver $identifierResolver,
        private readonly SymbolsRegistry $symbolsRegistry,
        private readonly EnrichedReflector $enrichedReflector,
    ) {
    }

    public function enterNode(Node $node): Node
    {
        if (!($node instanceof Identifier) || !ParentNodeAppender::hasParent($node)) {
            return $node;
        }

        $parent = ParentNodeAppender::getParent($node);

        $isClassOrInterface = $parent instanceof Class_ || $parent instanceof Interface_;

        if (!$isClassOrInterface) {
            return $node;
        }

        if (null === $parent->name) {
            throw UnexpectedParsingScenario::create();
        }

        $resolvedName = $this->identifierResolver->resolveIdentifier($node);

        if (!($resolvedName instanceof FullyQualified)) {
            throw UnexpectedParsingScenario::create();
        }

        if ($this->shouldBeAliased($resolvedName->toString())) {
            $this->symbolsRegistry->recordClass(
                $resolvedName,
                FullyQualifiedFactory::concat($this->prefix, $resolvedName),
            );
        }

        return $node;
    }

    private function shouldBeAliased(string $resolvedName): bool
    {
        if ($this->enrichedReflector->isExposedClass($resolvedName)) {
            return true;
        }

        // If is a function declaration, excluded global functions need to be
        // aliased since otherwise any usage without the FQCN in a namespace
        // will break. Indeed, previously it would work thanks to the function
        // PHP autoloading fallback mechanism, but now that the declaration is
        // namespaced because of the prefix, an alias is needed.
        return $this->enrichedReflector->belongsToGlobalNamespace($resolvedName)
            && $this->enrichedReflector->isClassExcluded($resolvedName);
    }
}
