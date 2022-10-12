<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser\Printer;

use PhpParser\Node;

interface Printer
{
    /**
     * @param Node[] $newStmts
     * @param Node[] $oldStmts
     * @param array<mixed> $oldTokens
     */
    public function print(array $newStmts, array $oldStmts, array $oldTokens): string;
}
