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

namespace Humbug\PhpScoper\PhpParser\Printer;

use PhpParser\Node;
use PhpParser\Token;

interface Printer
{
    /**
     * @param Node[]  $newStmts
     * @param Node[]  $oldStmts
     * @param Token[] $oldTokens
     */
    public function print(array $newStmts, array $oldStmts, array $oldTokens): string;
}
