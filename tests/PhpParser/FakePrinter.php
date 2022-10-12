<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser;

use Humbug\PhpScoper\PhpParser\Printer\Printer;
use LogicException;
use PhpParser\Node;

final class FakePrinter implements Printer
{
    public function print(array $newStmts, array $oldStmts, array $oldTokens): string
    {
        throw new LogicException();
    }
}
