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

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\NodeVisitor;
use Humbug\PhpScoper\NodeVisitor\Collection\NamespaceStmtCollection;
use Humbug\PhpScoper\NodeVisitor\Collection\UseStmtCollection;
use Humbug\PhpScoper\NodeVisitor\Resolver\FullyQualifiedNameResolver;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;

final class TraverserFactory
{
    /**
     * Functions for which the arguments will be prefixed.
     */
    public const WHITELISTED_FUNCTIONS = [
        'class_exists',
        'interface_exists',
    ];

    /**
     * @param string   $prefix            Prefix to apply to the files.
     * @param string[] $whitelist         List of classes to exclude from the scoping.
     * @param callable $globalWhitelister Closure taking a class name from the global namespace as an argument and
     *                                    returning a boolean which if `true` means the class should be scoped
     *                                    (i.e. is ignored) or scoped otherwise.
     *
     * @return NodeTraverserInterface
     */
    public function create(string $prefix, array $whitelist, callable $globalWhitelister): NodeTraverserInterface
    {
        $traverser = new NodeTraverser();

        $namespaceStatements = new NamespaceStmtCollection();
        $useStatements = new UseStmtCollection();

        $nameResolver = new FullyQualifiedNameResolver($namespaceStatements, $useStatements);

        $traverser->addVisitor(new NodeVisitor\UseStmt\GroupUseStmtTransformer());

        $traverser->addVisitor(new NodeVisitor\AppendParentNode());

        $traverser->addVisitor(new NodeVisitor\NamespaceStmtPrefixer($prefix, $namespaceStatements, $whitelist));

        $traverser->addVisitor(new NodeVisitor\UseStmt\UseStmtCollector($namespaceStatements, $useStatements));
        $traverser->addVisitor(new NodeVisitor\UseStmt\UseStmtPrefixer($prefix, $whitelist, $globalWhitelister));

        $traverser->addVisitor(new NodeVisitor\NameStmtPrefixer($prefix, $whitelist, $globalWhitelister, $nameResolver));
        $traverser->addVisitor(new NodeVisitor\StringScalarPrefixer($prefix, self::WHITELISTED_FUNCTIONS, $whitelist, $globalWhitelister, $nameResolver));

        $traverser->addVisitor(new NodeVisitor\WhitelistedClassAppender($whitelist));

        return $traverser;
    }
}
