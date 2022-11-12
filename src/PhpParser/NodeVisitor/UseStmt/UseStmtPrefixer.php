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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor\UseStmt;

use Humbug\PhpScoper\PhpParser\NodeVisitor\AttributeAppender\ParentNodeAppender;
use Humbug\PhpScoper\PhpParser\UnexpectedParsingScenario;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

/**
 * Prefixes the use statements.
 *
 * @private
 */
final class UseStmtPrefixer extends NodeVisitorAbstract
{
    public function __construct(
        private readonly string $prefix,
        private readonly EnrichedReflector $enrichedReflector,
    ) {
    }

    public function enterNode(Node $node): Node
    {
        if ($node instanceof UseUse && $this->shouldPrefixUseStmt($node)) {
            self::prefixStmt($node, $this->prefix);
        }

        return $node;
    }

    private function shouldPrefixUseStmt(UseUse $use): bool
    {
        $useType = self::findUseType($use);
        $nameString = $use->name->toString();

        $alreadyPrefixed = $this->prefix === $use->name->getFirst();

        if ($alreadyPrefixed) {
            return false;
        }

        if ($this->enrichedReflector->belongsToExcludedNamespace($nameString)) {
            return false;
        }

        if (Use_::TYPE_FUNCTION === $useType) {
            return !$this->enrichedReflector->isFunctionInternal($nameString);
        }

        if (Use_::TYPE_CONSTANT === $useType) {
            return !$this->enrichedReflector->isExposedConstant($nameString);
        }

        return Use_::TYPE_NORMAL !== $useType
            || !$this->enrichedReflector->isClassInternal($nameString);
    }

    private static function prefixStmt(UseUse $use, string $prefix): void
    {
        $previousName = $use->name;

        $prefixedName = Name::concat(
            $prefix,
            $use->name,
            $use->name->getAttributes(),
        );

        if (null === $prefixedName) {
            throw UnexpectedParsingScenario::create();
        }

        // Unlike the new (prefixed name), the previous name will not be
        // traversed hence we need to manually set its parent attribute
        ParentNodeAppender::setParent($previousName, $use);
        UseStmtManipulator::setOriginalName($use, $previousName);

        $use->name = $prefixedName;
    }

    /**
     * Finds the type of the use statement.
     *
     * @return int See \PhpParser\Node\Stmt\Use_ type constants.
     */
    private static function findUseType(UseUse $use): int
    {
        if (Use_::TYPE_UNKNOWN === $use->type) {
            /** @var Use_ $parentNode */
            $parentNode = ParentNodeAppender::getParent($use);

            return $parentNode->type;
        }

        return $use->type;
    }
}
