<?php

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser\NodeVisitor;

use Humbug\PhpScoper\PhpParser\StringNodePrefixer;
use PhpParser\Node;
use PhpParser\Node\Expr\Eval_;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;

final class EvalPrefixer extends NodeVisitorAbstract
{
    private StringNodePrefixer $stringPrefixer;

    public function __construct(StringNodePrefixer $stringPrefixer)
    {
        $this->stringPrefixer = $stringPrefixer;
    }

    public function enterNode(Node $node): Node
    {
        if ($node instanceof String_
            && ParentNodeAppender::findParent($node) instanceof Eval_
        ) {
            $this->stringPrefixer->prefixStringValue($node);
        }

        return $node;
    }
}
