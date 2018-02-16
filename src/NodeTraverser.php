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

namespace Humbug\PhpScoper;

// TODO: re-organise the classes location as this no longer makes sense. Not done here to try to keep the diff humanly
// readable.
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\InlineHTML;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeTraverser as PhpParserNodeTraverser;

final class NodeTraverser extends PhpParserNodeTraverser
{
    private $prefix;

    public function __construct(string $prefix)
    {
        parent::__construct();

        $this->prefix = $prefix;
    }

    /**
     * @inheritdoc
     */
    public function traverse(array $nodes)
    {
        $nodes = $this->wrapInNamespace($nodes);
        $nodes = $this->replaceGroupUseStatements($nodes);

        return parent::traverse($nodes);
    }

    /**
     * Wrap the statements in a namespace when necessary:.
     *
     * ```php
     * #!/usr/bin/env php
     * <?php declare(strict_types=1);
     *
     * // A small comment
     *
     * if (\true) {
     *  echo "yo";
     * }
     * ```
     *
     * Will result in:
     *
     * ```php
     * #!/usr/bin/env php
     * <?php declare(strict_types=1);
     *
     * // A small comment
     *
     * namespace {
     *     if (\true) {
     *      echo "yo";
     *     }
     * }
     * ```
     *
     * @param Node[] $nodes
     *
     * @return Node[]
     */
    private function wrapInNamespace(array $nodes): array
    {
        $realStatements = [];

        foreach ($nodes as $i => $node) {
            if ($node instanceof Declare_ || $node instanceof InlineHTML) {
                continue;
            }

            $firstRealStatementIndex = $i;
            $realStatements = array_slice($nodes, $i);

            break;
        }

        $firstRealStatement = current($realStatements);

        if (false !== $firstRealStatement && false === ($firstRealStatement instanceof Namespace_)) {
            $wrappedStatements = new Namespace_(null, $realStatements);

            array_splice($nodes, $firstRealStatementIndex, count($realStatements), [$wrappedStatements]);
        }

        return $nodes;
    }

    /**
     * @param Node[] $nodes
     *
     * @return Node[]
     */
    private function replaceGroupUseStatements(array $nodes): array
    {
        foreach ($nodes as $node) {
            if (false === ($node instanceof Namespace_)) {
                continue;
            }

            /** @var Namespace_ $node */
            $statements = $node->stmts;

            $newStatements = [];

            foreach ($statements as $statement) {
                if ($statement instanceof GroupUse) {
                    $uses_ = $this->createUses_($statement);

                    array_splice($newStatements, count($newStatements), 0, $uses_);
                } else {
                    $newStatements[] = $statement;
                }
            }

            $node->stmts = $newStatements;
        }

        return $nodes;
    }

    /**
     * @param GroupUse $node
     *
     * @return Use_[]
     */
    private function createUses_(GroupUse $node): array
    {
        return array_map(
            function (UseUse $use) use ($node): Use_ {
                $newUse = new UseUse(
                    Name::concat($node->prefix, $use->name, $use->name->getAttributes()),
                    $use->alias,
                    $use->type,
                    $use->getAttributes()
                );

                return new Use_(
                    [$newUse],
                    $node->type,
                    $node->getAttributes()
                );
            },
            $node->uses
        );
    }
}
