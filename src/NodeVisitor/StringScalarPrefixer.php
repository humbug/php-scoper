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
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;
use Roave\BetterReflection\Reflector\ClassReflector;

/**
 * Prefixes the string scalar values.
 *
 * ```
 * $x = 'Foo\Bar';
 * ```
 *
 * =>
 *
 * ```
 * $x = 'Humbug\Foo\Bar';
 * ```
 */
final class StringScalarPrefixer extends NodeVisitorAbstract
{
    private $prefix;
    private $whitelistedFunctions;
    private $whitelist;
    private $classReflector;

    /**
     * @param string         $prefix
     * @param string[]       $whitelistedFunctions
     * @param string[]       $whitelist
     * @param ClassReflector $classReflector
     */
    public function __construct(
        string $prefix,
        array $whitelistedFunctions,
        array $whitelist,
        ClassReflector $classReflector
    ) {
        $this->prefix = $prefix;
        $this->whitelistedFunctions = $whitelistedFunctions;
        $this->whitelist = $whitelist;
        $this->classReflector = $classReflector;
    }

    /**
     * @inheritdoc
     */
    public function enterNode(Node $node): Node
    {
        return ($this->shouldPrefixScalar($node))
            ? $this->prefixStringScalar($node)
            : $node
        ;
    }

    private function shouldPrefixScalar(Node $node): bool
    {
        if (false === ($node instanceof String_ && AppendParentNode::hasParent($node))) {
            return false;
        }
        /** @var String_ $node */
        $parentNode = AppendParentNode::getParent($node);

        if (false === ($parentNode instanceof Arg) || false === AppendParentNode::hasParent($parentNode)) {
            return false;
        }

        $argParent = AppendParentNode::getParent($parentNode);

        return
            $argParent instanceof FuncCall
            && $argParent->name instanceof Name
            && in_array((string) $argParent->name, $this->whitelistedFunctions)
        ;
    }

    private function prefixStringScalar(String_ $string): Node
    {
        $stringName = new Name(
            preg_replace('/^\\\\(.+)$/', '$1', $string->value),
            $string->getAttributes()
        );

        // Skip if is already prefixed
        if ($this->prefix === $stringName->getFirst()) {
            $newStringName = $stringName;
        // Check if the class can be prefixed: class from the global namespace
        } elseif (1 === count($stringName->parts)
            && false === $this->classReflector->reflect($stringName->toString())->isInternal()
        ) {
            $newStringName = $stringName;
        // Check if the class can be prefixed: regular class
        } elseif (1 < count($stringName->parts)
            && in_array($stringName->toString(), $this->whitelist)
        ) {
            $newStringName = $stringName;
        } else {
            $newStringName = FullyQualified::concat($this->prefix, $stringName->toString(), $stringName->getAttributes());
        }

        return new String_($newStringName->toString(), $string->getAttributes());
    }
}
