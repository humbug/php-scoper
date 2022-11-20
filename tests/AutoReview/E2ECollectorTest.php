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

namespace Humbug\PhpScoper\AutoReview;

use PHPUnit\Framework\TestCase;
use function count;
use function in_array;

/**
 * @covers \Humbug\PhpScoper\AutoReview\E2ECollector
 *
 * @internal
 */
class E2ECollectorTest extends TestCase
{
    public function test_it_collects_the_e2e_test_names(): void
    {
        $names = E2ECollector::getE2ENames();

        self::assertGreaterThan(0, count($names));

        foreach ($names as $name) {
            self::assertMatchesRegularExpression('/^e2e_\d{3}$/', $name);
        }
    }

    public function test_it_ignores_non_e2e_tests(): void
    {
        $names = E2ECollector::getE2ENames();

        self::assertFalse(in_array('e2e_000', $names, true));
    }
}
