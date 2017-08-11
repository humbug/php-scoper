<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor\UseStmt;

use Humbug\PhpScoper\NodeVisitor\UseStmtCollection;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\GroupUse;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

final class CollectUseStmtNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var UseStmtCollection
     */
    private $useStmtCollection;

    public function __construct(UseStmtCollection $useStmtCollection)
    {
        $this->useStmtCollection = $useStmtCollection;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if ($node instanceof Use_) {
            $this->useStmtCollection->add($node);
        }

        return $node;
    }
}