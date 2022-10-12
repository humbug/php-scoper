<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\PhpParser;

use UnexpectedValueException;

final class UnexpectedParsingScenario extends UnexpectedValueException
{
    public static function create(): self
    {
        return new self('Unexpected case. Please report it.');
    }
}
