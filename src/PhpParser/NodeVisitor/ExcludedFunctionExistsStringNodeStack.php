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

namespace Humbug\PhpScoper\PhpParser\NodeVisitor;

use Humbug\PhpScoper\PhpParser\Node\ClassAliasFuncCall;
use Humbug\PhpScoper\PhpParser\NodeVisitor\AttributeAppender\ParentNodeAppender;
use Humbug\PhpScoper\PhpParser\NodeVisitor\Resolver\IdentifierResolver;
use Humbug\PhpScoper\PhpParser\UnexpectedParsingScenario;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Switch_;
use PhpParser\Node\Stmt\TryCatch;
use PhpParser\NodeVisitorAbstract;
use function array_pop;
use function array_reduce;
use function in_array;

/**
 * Collection of String_ nodes which are contained within a `function_exists()` function call and for which the string
 * is an excluded function.
 *
 * Example of code for which the String_ node will be registered:
 *
 * ```
 * if (!function_exists('str_split')) {
 *     // ...
 * }
 *
 * @internal
 */
final class ExcludedFunctionExistsStringNodeStack extends NodeVisitorAbstract
{
    /**
     * @var String_[]
     */
    private array $stack = [];

    // TODO: cases to handle
    // - what if is a variable instead?
    // - multiple function_exists already

    public function push(String_ $string): void
    {
        $this->stack[] = $string;
    }

    /**
     * @return String_[]
     */
    public function fetch(): array
    {
        $stack = $this->stack;

        $this->stack = [];

        return $stack;
    }
}
