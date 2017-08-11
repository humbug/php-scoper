<?php
declare(strict_types=1);

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

        $useStatementsCollection = new UseStmtCollection();
        $whitelistedStatements = new WhitelistedStatements();

        $this->traverser->addVisitor(new NodeVisitor\ParentNodeVisitor());

        $this->traverser->addVisitor(new NodeVisitor\IgnoreNamespaceScoperNodeVisitor($whitelist, $globalWhitelister));
        $this->traverser->addVisitor(new NodeVisitor\ScopeNamespaceStmtNodeVisitor($prefix));

        $this->traverser->addVisitor(new NodeVisitor\UseStmt\CollectUseStmtNodeVisitor($useStatementsCollection));
        $this->traverser->addVisitor(new NodeVisitor\UseStmt\ScopeUseStmtNodeVisitor($prefix, $whitelist, $whitelistedStatements));
        $this->traverser->addVisitor(new NodeVisitor\UseStmt\ScopeSingleLevelUseAliasVisitor($prefix));
        $this->traverser->addVisitor(new NodeVisitor\UseStmt\ScopeGroupUseStmtNodeVisitor($prefix));

        $this->traverser->addVisitor(new NodeVisitor\FullyQualified\ScopeFullyQualifiedNodeVisitor($prefix));

        $this->traverser->addVisitor(new NodeVisitor\ScopeWhitelistedElementsFromGlobalNamespaceNodeVisitor($prefix, $globalWhitelister));
        $this->traverser->addVisitor(new NodeVisitor\ScopeFunctionCallStmtNodeVisitor($prefix, $whitelist, self::WHITELISTED_FUNCTIONS));
        $this->traverser->addVisitor(new NodeVisitor\ScopeNewStmtNodeVisitor($prefix, $useStatementsCollection, $whitelist));
        $this->traverser->addVisitor(new NodeVisitor\ScopeStaticCallStmtNodeVisitor($prefix, $useStatementsCollection, $whitelist));

        return $this->traverser;
    }
}