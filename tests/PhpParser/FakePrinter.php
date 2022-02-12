<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser;

use Humbug\PhpScoper\PhpParser\Printer\Printer;
use LogicException;
use PhpParser\Node;

final class FakePrinter implements Printer
{
    public function print(array $statements): string
    {
        throw new LogicException();
    }
}
