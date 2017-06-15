<?php
declare(strict_types=1);

namespace Humbug\PhpScoper\Scoper;

use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Throwable\Exception\ParsingException;

final class FakeScoper implements Scoper
{
    /**
     * @inheritdoc
     */
    public function scope(string $filePath, string $prefix): string
    {
        throw new \LogicException();
    }
}