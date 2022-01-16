<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Patcher;

use function array_reduce;

interface Patcher
{
    public function __invoke(string $filePath, string $prefix, string $contents): string;
}
