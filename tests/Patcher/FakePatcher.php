<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Patcher;

use LogicException;

final class FakePatcher implements Patcher
{
    public function __invoke(string $filePath, string $prefix, string $contents): string
    {
        throw new LogicException('Did not expect to be called');
    }
}
