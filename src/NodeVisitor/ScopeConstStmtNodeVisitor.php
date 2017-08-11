<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor;

use Humbug\PhpScoper\NodeVisitor\NamespaceStmtCollection;
use Humbug\PhpScoper\NodeVisitor\UseStmtCollection;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

final class ScopeConstStmtNodeVisitor extends NodeVisitorAbstract
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
        if (false === ($node instanceof ClassConstFetch)) {
            return $node;
        }
        /** @var ClassConstFetch $node */

        $constClassNode = $node->class;

        if (false === ($constClassNode instanceof Name)) {
            return $node;
        }
        /** @var Name $useStatement */

        if ($node->hasAttribute('parent') && $node->getAttribute('parent') instanceof Arg) {
            return $node;
        }

        $useStatement = $this->useStmtCollection->findStatementForName($constClassNode->getFirst());

        $prefix = false;

        if (null === $useStatement) {
            if (0 === count($this->namespaceStatements)) {
                $newClassNode = new FullyQualified($constClassNode, $constClassNode->getAttributes());

                $prefix = true;
            } else {
                $namespaceStatement = $this->namespaceStatements->getNamespaceName();

                $newClassNode = FullyQualified::concat($namespaceStatement, $constClassNode, $constClassNode->getAttributes());
            }
        } else {
            $newClassNode = FullyQualified::concat($useStatement, $constClassNode->slice(1), $constClassNode->getAttributes());
        }

        $newClassNode->setAttribute('phpscoper_ignore', true);

        if (in_array((string) $newClassNode, $this->whitelist)) {
            // Continue
        } elseif ($prefix) {
            $newClassNode = FullyQualified::concat($this->prefix, $newClassNode, $newClassNode->getAttributes());
        } else {
            return $node;
        }

        return new ClassConstFetch($newClassNode, $node->name, $node->getAttributes());
    }
}