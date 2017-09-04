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

namespace Humbug\PhpScoper\NodeVisitor\UseStmt;

use Humbug\PhpScoper\NodeVisitor\UseStmtCollection;
use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeVisitorAbstract;

final class CollectUseStmtNodeVisitor extends NodeVisitorAbstract
{
    private $useStatements;

    public function __construct(UseStmtCollection $useStatements)
    {
        $this->useStatements = $useStatements;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        if ($node instanceof Use_) {
            $this->useStatements->add($node);
        }

        return $node;
    }
}
