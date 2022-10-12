<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Patcher;

use function Safe\sprintf;

final class DummyPatcher implements Patcher
{
    public function __invoke(string $filePath, string $prefix, string $contents): string
    {
        return sprintf('patchedContent<%s>', $contents);
    }
}
