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

final class FunctionCallScoperNodeVisitor extends NodeVisitorAbstract
{
    private $prefix;
    private $functions;

    /**
     * @param string   $prefix
     * @param string[] $functions Functions which first parameter should be prefixed.
     */
    public function __construct($prefix, array $functions)
    {
        $this->prefix = $prefix;
        $this->functions = $functions;
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

        if (!in_array($node->name->getFirst(), $this->functions)) {
            return $node;
        }

        $value = $node->args[0]->value;
        if (!$value instanceof Node\Scalar\String_) {
            return $node;
        }

        $stringValue = ltrim($value->value, '\\');

        // Do not prefix global namespace
        if (false !== strstr($stringValue, '\\')) {
            $value->value = ($value->value !== $stringValue ? '\\' : '').$this->prefix.'\\'.$stringValue;
        }

        return $node;
    }
}
