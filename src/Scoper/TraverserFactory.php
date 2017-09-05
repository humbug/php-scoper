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

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\NodeVisitor;
use Humbug\PhpScoper\NodeVisitor\Collection\UseStmtCollection;
use Humbug\PhpScoper\NodeVisitor\WhitelistedStatements;
use PhpParser\NodeTraverser;
use PhpParser\NodeTraverserInterface;

interface TraverserFactory
{
    /**
     * @param string   $prefix Prefix to apply to the files.
     * @param string[] $whitelist List of classes to exclude from the scoping.
     * @param callable $globalWhitelister Closure taking a class name from the global namespace as an argument and
     *                                    returning a boolean which if `true` means the class should be scoped
     *                                    (i.e. is ignored) or scoped otherwise.
     *
     * @return NodeTraverserInterface
     */
    public function create(string $prefix, array $whitelist, callable $globalWhitelister): NodeTraverserInterface;
}
