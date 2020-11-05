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
use function array_key_exists;
use function count;
use function implode;
use function strtolower;

/**
 * Utility class collecting all the use statements for the scoped files allowing to easily find the use which a node
 * may use.
 *
 * @private
 */
final class UseStmtCollection implements IteratorAggregate
{
    private $hashes = [];

    /**
     * @var Use_[][]
     */
    private $nodes = [
        null => [],
    ];

    public function add(?Name $namespaceName, Use_ $use): void
    {
        $this->nodes[(string) $namespaceName][] = $use;
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

        $isFunctionName = $this->isFunctionName($node, $parentNode);
        $isConstantName = $this->isConstantName($node, $parentNode);

        $hash = implode(
            ':',
            [
                $namespaceName ? $namespaceName->toString() : '',
                $name,
                $isFunctionName ? 'func' : '',
                $isConstantName ? 'const' : '',
            ]
        );

        if (array_key_exists($hash, $this->hashes)) {
            return $this->hashes[$hash];
        }

        return $this->hashes[$hash] = $this->find(
            $this->nodes[(string) $namespaceName] ?? [],
            $isFunctionName,
            $isConstantName,
            $name
        );
    }

    /**
     * @inheritdoc
     */
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->nodes);
    }

    private function find(array $useStatements, bool $isFunctionName, bool $isConstantName, string $name): ?Name
    {
        foreach ($useStatements as $use_) {
            foreach ($use_->uses as $useStatement) {
                if (false === ($useStatement instanceof UseUse)) {
                    continue;
                }

                $type = Use_::TYPE_UNKNOWN !== $use_->type ? $use_->type : $useStatement->type;

                if ($name === $useStatement->getAlias()->toLowerString()) {
                    if ($isFunctionName) {
                        if (Use_::TYPE_FUNCTION === $type) {
                            return UseStmtManipulator::getOriginalName($useStatement);
                        }

                        continue;
                    }

                    if ($isConstantName) {
                        if (Use_::TYPE_CONSTANT === $type) {
                            return UseStmtManipulator::getOriginalName($useStatement);
                        }

                        continue;
                    }

                    if (Use_::TYPE_NORMAL === $type) {
                        // Match the alias
                        return UseStmtManipulator::getOriginalName($useStatement);
                    }
                }
            }
        }

        return null;
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

    private function isConstantName(Name $node, ?Node $parentNode): bool
    {
        return $parentNode instanceof ConstFetch && 1 === count($node->parts);
    }
}
