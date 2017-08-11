<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor;

use Humbug\PhpScoper\NodeVisitor\UseStmtCollection;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

final class CollectNamespaceStmtNodeVisitor extends NodeVisitorAbstract
{
    private $namespaceStatements;

    public function __construct(NamespaceStmtCollection $namespaceStatements)
    {
        $this->namespaceStatements = $namespaceStatements;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if ($node instanceof Namespace_) {
            $this->namespaceStatements->add($node);
        }

        return $node;
    }
}