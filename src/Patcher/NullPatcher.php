<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Patcher;

final class NullPatcher implements Patcher
{
    public function __invoke(string $filePath, string $prefix, string $contents): string
    {
        return $contents;
    }
}
