<?php

declare(strict_types=1);

namespace Humbug\PhpScoper;

use PHPUnit\Framework\Assert;

final class PhpScoperAssertions
{
    /**
     * @parma list $expected
     */
    public static function assertListEqualsCanonicalizing(
        array $expected,
        $actual,
        string $message = ''
    ): void {
        Assert::assertIsArray($actual);

        // TODO: contribute this to PHPUnit
        // TODO: use assertArrayIsList() assertion once available
        // https://github.com/sebastianbergmann/phpunit/commit/71f507496aa1a483b32d9257d6f3477e6e5c091d
        Assert::assertTrue(
            array_is_list($actual),
            var_export($actual, true),
        );

        Assert::assertEqualsCanonicalizing(
            $expected,
            $actual,
            $message,
        );
    }

    private function __construct()
    {
    }
}
