<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoper;

use PHPUnit\Framework\Assert;
use function array_is_list;
use function var_export;

final class PhpScoperAssertions
{
    /**
     * @parma list $expected
     */
    public static function assertListEqualsCanonicalizing(
        array $expected,
        mixed $actual,
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
