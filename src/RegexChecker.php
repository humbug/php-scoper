<?php

declare(strict_types=1);

namespace Humbug\PhpScoper;

use function count;
use function current;
use function preg_last_error;
use function preg_last_error_msg;
use function preg_match as native_preg_match;
use function Safe\sprintf;
use function str_contains;
use function strlen;
use function trim;

// TODO: move this under a Configuration namespace
final class RegexChecker
{
    public function isRegexLike(string $value): bool
    {
        $valueLength = strlen($value);

        if ($valueLength < 2) {
            return false;
        }

        $firstCharacter = $value[0];
        $lastCharacter = $value[$valueLength - 1];

        if ($firstCharacter !== $lastCharacter) {
            return false;
        }

        $trimmedValue = trim($value, $firstCharacter);

        if (strlen($trimmedValue) !== ($valueLength - 2)) {
            return false;
        }

        if (str_contains($trimmedValue, $firstCharacter)) {
            return false;
        }

        return true;
    }

    public function validateRegex(string $regex): ?string
    {
        if (@native_preg_match($regex, '') !== false) {
            return null;
        }

        return sprintf(
            'Invalid regex: %s (code %s)',
            preg_last_error_msg(),
            preg_last_error(),
        );
    }
}
