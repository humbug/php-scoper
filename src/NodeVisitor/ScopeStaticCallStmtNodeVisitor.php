<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\NodeVisitorAbstract;

final class ScopeStaticCallStmtNodeVisitor extends NodeVisitorAbstract
{
    private $prefix;
    private $useStmtCollection;
    private $whitelist;

    public function __construct(string $prefix, UseStmtCollection $useStmtCollection, array $whitelist)
    {

        $this->prefix = $prefix;
        $this->useStmtCollection = $useStmtCollection;
        $this->whitelist = $whitelist;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if (false === ($node instanceof Name) || false === $node->hasAttribute('parent')) {
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

        $useStatement = $this->findUseStatement($node->getFirst());

        if (null === $useStatement) {
            return $node;
        }

        $newNode = FullyQualified::concat($useStatement, $node->slice(1), $node->getAttributes());

        if (in_array((string) $newNode, $this->whitelist)) {
            return $newNode;
        }

        return $node;
    }

    private function findUseStatement(string $name): ?Name
    {
        foreach ($this->useStmtCollection as $use_) {
            foreach ($use_->uses as $useStatement) {
                if ($useStatement instanceof UseUse) {
                    if ($name === $useStatement->alias || $name === $useStatement->name->getLast()) {
                        return $useStatement->name;
                    }
                }

                //TODO
            }
        }

        return null;
    }
}