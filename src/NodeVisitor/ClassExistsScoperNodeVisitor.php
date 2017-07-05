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

namespace Humbug\PhpScoper\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

final class ClassExistsScoperNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @var string
     */
    private $prefix;

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof Node\Expr\FuncCall || null === $node->name) {
            return $node;
        }

        if (!$node->name instanceof Node\Name) {
            return $node;
        }

        if ('class_exists' !== $node->name->getFirst()) {
            return $node;
        }

        $value = $node->args[0]->value;
        if (!$value instanceof Node\Scalar\String_) {
            return $node;
        }

        $value->value = $this->prefix.'\\'.$value->value;

        return $node;
    }
}
