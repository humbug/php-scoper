<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser\Printer;

use PhpParser\Node;

interface Printer
{
    /**
     * @param Node[] $statements
     */
    public function print(array $statements): string;
}
