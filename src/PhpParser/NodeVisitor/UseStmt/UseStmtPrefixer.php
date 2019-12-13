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
    private $prefix;
    private $whitelist;
    private $reflector;

    public function __construct(string $prefix, Whitelist $whitelist, Reflector $reflector)
    {
        $this->prefix = $prefix;
        $this->reflector = $reflector;
        $this->whitelist = $whitelist;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof UseUse && $this->shouldPrefixUseStmt($node)) {
            $previousName = $node->name;

            /** @var Name $prefixedName */
            $prefixedName = Name::concat($this->prefix, $node->name, $node->name->getAttributes());
            $node->name = $prefixedName;

            UseStmtManipulator::setOriginalName($node, $previousName);
        }

        return $node;
    }

    private function shouldPrefixUseStmt(UseUse $use): bool
    {
        $useType = $this->findUseType($use);

        // If is already from the prefix namespace
        if ($this->prefix === $use->name->getFirst()) {
            return false;
        }

        // If is whitelisted
        if ($this->whitelist->belongsToWhitelistedNamespace((string) $use->name)) {
            return false;
        }

        if (Use_::TYPE_FUNCTION === $useType) {
            return false === $this->reflector->isFunctionInternal((string) $use->name);
        }

        if (Use_::TYPE_CONSTANT === $useType) {
            return
                false === $this->whitelist->isSymbolWhitelisted((string) $use->name)
                && false === $this->reflector->isConstantInternal((string) $use->name)
            ;
        }

        return Use_::TYPE_NORMAL !== $useType || false === $this->reflector->isClassInternal((string) $use->name);
    }

    /**
     * Finds the type of the use statement.
     *
     * @param UseUse $use
     *
     * @return int See \PhpParser\Node\Stmt\Use_ type constants.
     */
    private function findUseType(UseUse $use): int
    {
        if (Use_::TYPE_UNKNOWN === $use->type) {
            /** @var Use_ $parentNode */
            $parentNode = ParentNodeAppender::getParent($use);

            return $parentNode->type;
        }

        return $use->type;
    }
}
