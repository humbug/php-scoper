<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor\FunctionStmt;

use Humbug\PhpScoper\NodeVisitor\NamespaceStmtCollection;
use Humbug\PhpScoper\NodeVisitor\UseStmtCollection;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

final class ScopeStaticCallStmtNodeVisitor extends NodeVisitorAbstract
{
    private $prefix;
    private $namespaceStatements;
    private $useStmtCollection;
    private $whitelist;

    public function __construct(
        string $prefix,
        NamespaceStmtCollection $namespaceStatements,
        UseStmtCollection $useStatements,
        array $whitelist
    ) {

        $this->prefix = $prefix;
        $this->namespaceStatements = $namespaceStatements;
        $this->useStmtCollection = $useStatements;
        $this->whitelist = $whitelist;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if (false === ($node instanceof Name)
            || false === $node->hasAttribute('parent')
            || ($node->hasAttribute('phpscoper_ignore') && $node->getAttribute('phpscoper_ignore'))
        ) {
            return $node;
        }

        $parentNode = $node->getAttribute('parent');

        if (false === ($parentNode instanceof StaticCall)) {
            return $node;
        }

        if (1 === count($node->parts)) {
            //TODO
            $x = '';
        }

        $useStatement = $this->useStmtCollection->findStatementForName($node->getFirst());

        $prefix = false;

        if (null === $useStatement) {
            if (0 === count($this->namespaceStatements)) {
                $newNodeClass = new FullyQualified($node, $node->getAttributes());

                $prefix = (false === in_array((string) $newNodeClass, $this->whitelist));
            } else {
                $namespaceStatement = $this->namespaceStatements->getNamespaceName();

                $newNodeClass = FullyQualified::concat($namespaceStatement, $node, $node->getAttributes());

                if (false === in_array((string) $newNodeClass, $this->whitelist)) {
                    return $node;
                }
            }
        } else {
            $newNodeClass = FullyQualified::concat($useStatement, $node->slice(1), $node->getAttributes());

            if (false === in_array((string) $newNodeClass, $this->whitelist)) {
                return $node;
            }
        }

        $newNodeClass->setAttribute('phpscoper_ignore', true);

        if ($prefix) {
            return FullyQualified::concat($this->prefix, $newNodeClass, $newNodeClass->getAttributes());
        }

        return $newNodeClass;
    }
}