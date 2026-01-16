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

namespace Humbug\PhpScoper\Console;

use Fidry\Console\Test\OutputAssertions;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @psalm-require-implements AppTesterTestCase
 * @psalm-require-extends TestCase
 */
trait AppTesterAbilities
{
    private ApplicationTester $appTester;

    public function getAppTester(): ApplicationTester
    {
        return $this->appTester;
    }

    /**
     * @param callable(string):string $extraNormalizers
     */
    private function assertExpectedOutput(
        string $expectedOutput,
        int $expectedStatusCode,
        callable ...$extraNormalizers,
    ): void {
        OutputAssertions::assertSameOutput(
            $expectedOutput,
            $expectedStatusCode,
            $this->getAppTester(),
            DisplayNormalizer::normalize(...),
            ...$extraNormalizers,
        );
    }
}
