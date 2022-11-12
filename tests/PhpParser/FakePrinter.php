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

namespace Humbug\PhpScoper\PhpParser;

use Humbug\PhpScoper\PhpParser\Printer\Printer;
use LogicException;

final class FakePrinter implements Printer
{
    public function print(array $newStmts, array $oldStmts, array $oldTokens): string
    {
        throw new LogicException();
    }
}
