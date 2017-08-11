<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor\NewStmt;

use Humbug\PhpScoper\NodeVisitor\NamespaceStmtCollection;
use Humbug\PhpScoper\NodeVisitor\UseStmtCollection;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

final class ScopeSingleLevelNewStmtNodeVisitor extends NodeVisitorAbstract
{
    private $prefix;
    private $namespaceStatements;
    private $useStatements;
    private $whitelist;

    public function __construct(
        string $prefix,
        NamespaceStmtCollection $namespaceStatements,
        UseStmtCollection $useStatements,
        array $whitelist
    ) {

        $this->prefix = $prefix;
        $this->namespaceStatements = $namespaceStatements;
        $this->useStatements = $useStatements;
        $this->whitelist = $whitelist;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if (false === ($node instanceof New_)) {
            return $node;
        }
        /** @var New_ $node */

        if (false === ($node->class instanceof Name)) {
            return $node;
        }

        /** @var Name $nodeClass */
        $nodeClass = $node->class;

        if (1 !== count($nodeClass->parts)) {
            return $node;
        }

        $useStatement = $this->useStatements->findStatementForName($nodeClass->getFirst());

        if (null === $useStatement) {
            if (0 === count($this->namespaceStatements)) {
                return $node;
            }

            $namespaceStatement = $this->namespaceStatements->getNamespaceName();

            $newNodeClass = FullyQualified::concat($namespaceStatement, $nodeClass, $nodeClass->getAttributes());
        } else {
            $newNodeClass = FullyQualified::concat($useStatement, $nodeClass->slice(1), $nodeClass->getAttributes());
        }

        $newNodeClass->setAttribute('phpscoper_ignore', true);

        if (in_array((string) $newNodeClass, $this->whitelist)) {
            return new New_($newNodeClass, $node->args, $node->getAttributes());
        }

        return $node;
    }
}