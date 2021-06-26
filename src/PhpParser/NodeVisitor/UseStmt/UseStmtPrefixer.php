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

use Humbug\PhpScoper\PhpParser\NodeVisitor\ParentNodeAppender;
use Humbug\PhpScoper\Reflector;
use Humbug\PhpScoper\Whitelist;
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
    private string $prefix;
    private Whitelist $whitelist;
    private Reflector $reflector;

    public function __construct(
        string $prefix,
        Whitelist $whitelist,
        Reflector $reflector
    ) {
        $this->prefix = $prefix;
        $this->reflector = $reflector;
        $this->whitelist = $whitelist;
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

        // If is already from the prefix namespace
        if ($this->prefix === $use->name->getFirst()) {
            return false;
        }

        // If is whitelisted
        if ($this->whitelist->belongsToWhitelistedNamespace((string) $use->name)) {
            return false;
        }

        if (Use_::TYPE_FUNCTION === $useType) {
            return !$this->reflector->isFunctionInternal((string) $use->name);
        }

        if (Use_::TYPE_CONSTANT === $useType) {
            return self::shouldPrefixConstantUseStmt(
                $use->name->toString(),
                $this->whitelist,
                $this->reflector,
            );
        }

        return Use_::TYPE_NORMAL !== $useType
            || !$this->reflector->isClassInternal((string) $use->name);
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
            return;
        }

        // Unlike the new (prefixed name), the previous name will not be
        // traversed hence we need to manually set its parent attribute
        ParentNodeAppender::setParent($previousName, $use);
        UseStmtManipulator::setOriginalName($use, $previousName);

        $use->name = $prefixedName;
    }

    private static function shouldPrefixConstantUseStmt(
        string $name,
        Whitelist $whitelist,
        Reflector $reflector
    ): bool {
        return !$whitelist->isGlobalWhitelistedConstant($name)
            && !$whitelist->isSymbolWhitelisted($name, true)
            && !$reflector->isConstantInternal($name);
    }

    /**
     * Finds the type of the use statement.
     *
     * @param UseUse $use
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
