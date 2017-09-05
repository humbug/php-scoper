<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor\Resolver;

use Humbug\PhpScoper\NodeVisitor\Collection\NamespaceStmtCollection;
use Humbug\PhpScoper\NodeVisitor\Collection\UseStmtCollection;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;

final class FullyQualifiedNameResolver
{
    private $namespaceStatements;
    private $useStatements;

    public function __construct(NamespaceStmtCollection $namespaceStatements, UseStmtCollection $useStatements)
    {
        $this->namespaceStatements = $namespaceStatements;
        $this->useStatements = $useStatements;
    }

    /**
     * Attempts to resolve the node name into a fully qualified node. Returns a valid name node on failure.
     *
     * @param Name $node
     *
     * @return Name|FullyQualified
     */
    public function resolveName(Name $node): Name
    {
        if ($node instanceof FullyQualified) {
            return $node;
        }

        $useStatement = $this->useStatements->findStatementForNode($node);

        if (null !== $useStatement) {
            return FullyQualified::concat($useStatement, $node->slice(1), $node->getAttributes());
        }

        $namespaceStatement = $this->namespaceStatements->findNamespaceForNode($node);

        if (null !== $namespaceStatement) {
            return FullyQualified::concat($namespaceStatement, $node, $node->getAttributes());
        }

        return new FullyQualified($node, $node->getAttributes());
    }
}