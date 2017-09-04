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
use Humbug\PhpScoper\NodeVisitor\UseStmtCollection;
use Humbug\PhpScoper\NodeVisitor\WhitelistedStatements;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;

/**
 * @internal
 */
final class TraverserFactory
{
    /**
     * Functions for which the arguments will be prefixed.
     */
    const WHITELISTED_FUNCTIONS = [
        'class_exists',
        'interface_exists',
    ];

    private $traverser;

    public function create(string $prefix, array $whitelist, callable $globalWhitelister): NodeTraverserInterface
    {
        if (null !== $this->traverser) {
            return $this->traverser;
        }

        $this->traverser = new NodeTraverser();

        $namespaceStatements = new NodeVisitor\NamespaceStmtCollection();
        $useStatements = new UseStmtCollection();
        $whitelistedStatements = new WhitelistedStatements();

        $this->traverser->addVisitor(new NodeVisitor\ParentNodeVisitor());

        $this->traverser->addVisitor(new NodeVisitor\CollectNamespaceStmtNodeVisitor($namespaceStatements));
        $this->traverser->addVisitor(new NodeVisitor\IgnoreNamespaceScoperNodeVisitor($whitelist, $globalWhitelister));
        $this->traverser->addVisitor(new NodeVisitor\ScopeNamespaceStmtNodeVisitor($prefix));

        $this->traverser->addVisitor(new NodeVisitor\UseStmt\CollectUseStmtNodeVisitor($useStatements));
        $this->traverser->addVisitor(new NodeVisitor\UseStmt\ScopeUseStmtNodeVisitor($prefix, $whitelist, $whitelistedStatements));
        $this->traverser->addVisitor(new NodeVisitor\UseStmt\ScopeSingleLevelUseAliasVisitor($prefix));
        $this->traverser->addVisitor(new NodeVisitor\UseStmt\ScopeGroupUseStmtNodeVisitor($prefix));

        $this->traverser->addVisitor(new NodeVisitor\ScopeFullyQualifiedNodeVisitor($prefix));
        $this->traverser->addVisitor(new NodeVisitor\ScopeWhitelistedElementsFromGlobalNamespaceNodeVisitor($prefix, $globalWhitelister));
        $this->traverser->addVisitor(new NodeVisitor\ScopeConstStmtNodeVisitor($prefix, $namespaceStatements, $useStatements, $whitelist));

        $this->traverser->addVisitor(new NodeVisitor\NewStmt\ScopeNewStmtNodeVisitor($prefix, $namespaceStatements, $useStatements, $whitelist));
        $this->traverser->addVisitor(new NodeVisitor\NewStmt\ScopeSingleLevelNewStmtNodeVisitor($prefix, $namespaceStatements, $useStatements, $whitelist));

        $this->traverser->addVisitor(new NodeVisitor\FunctionStmt\ScopeFunctionCallArgumentsStmtNodeVisitor($prefix, $whitelist, self::WHITELISTED_FUNCTIONS));
        $this->traverser->addVisitor(new NodeVisitor\FunctionStmt\ScopeStaticCallStmtNodeVisitor($prefix, $namespaceStatements, $useStatements, $whitelist));
        $this->traverser->addVisitor(new NodeVisitor\FunctionStmt\ScopeFunctionCallStmtNodeVisitor($prefix, $namespaceStatements, $useStatements, $whitelist));

        return $this->traverser;
    }
}
