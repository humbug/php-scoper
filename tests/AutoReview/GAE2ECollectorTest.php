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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use function count;

/**
 * @internal
 */
#[CoversClass(GAE2ECollector::class)]
class GAE2ECollectorTest extends TestCase
{
    public function test_it_collects_the_e2e_test_names(): void
    {
        $names = GAE2ECollector::getExecutedE2ETests();

        self::assertGreaterThan(0, count($names));

        foreach ($names as $name) {
            self::assertMatchesRegularExpression('/^e2e_\d{3}$/', $name);
        }
    }

    public function test_it_ignores_non_e2e_tests(): void
    {
        $names = GAE2ECollector::getExecutedE2ETests();

        self::assertNotContains('e2e_000', $names);
    }
}
