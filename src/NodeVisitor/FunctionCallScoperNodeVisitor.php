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
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;

final class FunctionCallScoperNodeVisitor extends NodeVisitorAbstract
{
    private $prefix;
    private $whitelist;
    private $functions;

    /**
     * @param string   $prefix
     * @param string[] $whitelist
     * @param string[] $functions Functions which first parameter should be prefixed.
     */
    public function __construct(string $prefix, array $whitelist, array $functions)
    {
        $this->prefix = $prefix;
        $this->whitelist = $whitelist;
        $this->functions = $functions;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node)
    {
        if (!$node instanceof FuncCall || null === $node->name) {
            return $node;
        }

        if (!$node->name instanceof Name) {
            return $node;
        }

        if (!in_array($node->name->getFirst(), $this->functions)) {
            return $node;
        }

        $value = $node->args[0]->value;
        if (!$value instanceof String_) {
            return $node;
        }

        $stringValue = ltrim($value->value, '\\');

        // Do not prefix whitelisted classes
        if (in_array($stringValue, $this->whitelist)) {
            return $node;
        }

        // Do not prefix global namespace
        if (false !== strstr($stringValue, '\\')) {
            $value->value = ($value->value !== $stringValue ? '\\' : '').$this->prefix.'\\'.$stringValue;
        }

        return $node;
    }
}
