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

namespace Humbug\PhpScoper\NodeVisitor\UseStmt;

use Humbug\PhpScoper\NodeVisitor\AppendParentNode;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

/**
 * Prefixes the use statements.
 *
 *
 */
final class UseStmtPrefixer extends NodeVisitorAbstract
{
    private $prefix;
    private $whitelist;
    private $globalWhitelister;

    /**
     * @param string   $prefix
     * @param string[] $whitelist
     * @param callable $globalWhitelister
     */
    public function __construct(string $prefix, array $whitelist, callable $globalWhitelister)
    {
        $this->prefix = $prefix;
        $this->whitelist = $whitelist;
        $this->globalWhitelister = $globalWhitelister;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof UseUse && $this->shouldPrefixUseStmt($node)) {
            $node->name = Name::concat($this->prefix, $node->name);
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

        // Is not from the Composer namespace
        if ('Composer' === $use->name->getFirst()) {
            return false;
        }

        if (1 === count($use->name->parts)) {
            return (
                $useType !== Use_::TYPE_NORMAL
                || ($this->globalWhitelister)($use->name->getFirst())
            );
        }

        return false === (
            $useType === Use_::TYPE_NORMAL
            && in_array((string) $use->name, $this->whitelist)
        );
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
            $parentNode = AppendParentNode::getParent($use);

            return $parentNode->type;
        }

        return $use->type;
    }
}
