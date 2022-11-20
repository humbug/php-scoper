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

/**
 * @coversNothing
 *
 * @internal
 */
class GAE2ETest extends TestCase
{
    public function test_github_actions_executes_all_the_e2e_tests(): void
    {
        $expected = E2ECollector::getE2ENames();
        $actual = GAE2ECollector::getExecutedE2ETests();

        self::assertEqualsCanonicalizing($expected, $actual);
    }
}
