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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor\Collection;

use ArrayIterator;
use Humbug\PhpScoper\PhpParser\Node\NamedIdentifier;
use Humbug\PhpScoper\PhpParser\NodeVisitor\ParentNodeAppender;
use IteratorAggregate;
use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use function count;
use function Humbug\PhpScoper\clone_node;

/**
 * Utility class collecting all the use statements for the scoped files allowing to easily find the use which a node
 * may use.
 *
 * @private
 */
final class UseStmtCollection implements IteratorAggregate
{
    /**
     * @var Use_[][]
     */
    private $nodes = [
        null => [],
    ];

    public function add(?Name $namespaceName, Use_ $node): void
    {
        $this->nodes[(string) $namespaceName][] = clone_node($node);
    }

    /**
     * Finds the statements matching the given name.
     *
     * $name = 'Foo';
     *
     * use X;
     * use Bar\Foo;
     * use Y;
     *
     * will return the use statement for `Bar\Foo`.
     *
     * @param Name|null $namespaceName
     * @param Name      $node
     *
     * @return null|Name
     */
    public function findStatementForNode(?Name $namespaceName, Name $node): ?Name
    {
        $name = strtolower($node->getFirst());

        $parentNode = ParentNodeAppender::findParent($node);

        if ($parentNode instanceof ClassLike
            && $node instanceof NamedIdentifier
            && $node->getOriginalNode() === $parentNode->name
        ) {
            // The current node can either be the class like name or one of its elements, e.g. extends or implements.
            // In the first case, the node was original an Identifier.

            return null;
        }

        $useStatements = $this->nodes[(string) $namespaceName] ?? [];

        foreach ($useStatements as $use_) {
            foreach ($use_->uses as $useStatement) {
                if (!($useStatement instanceof UseUse)) {
                    continue;
                }

                if ($name === $useStatement->getAlias()->toLowerString()) {
                    if ($this->isFunctionName($node, $parentNode)) {
                        if (Use_::TYPE_FUNCTION === $use_->type) {
                            return $useStatement->name;
                        }

                        continue;
                    }

                    if ($parentNode instanceof ConstFetch && 1 === count($node->parts)) {
                        if (Use_::TYPE_CONSTANT === $use_->type) {
                            return $useStatement->name;
                        }

                        continue;
                    }

                    // Match the alias
                    return $useStatement->name;
                }

                if (null !== $useStatement->alias) {
                    continue;
                }
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->nodes);
    }

    private function isFunctionName(Name $node, ?Node $parentNode): bool
    {
        if (null === $parentNode || 1 !== count($node->parts)) {
            return false;
        }

        if ($parentNode instanceof FuncCall) {
            return true;
        }

        if (false === ($parentNode instanceof Function_)) {
            return false;
        }
        /* @var Function_ $parentNode */

        return $node instanceof NamedIdentifier && $node->getOriginalNode() === $parentNode->name;
    }
}
