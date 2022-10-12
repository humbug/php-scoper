<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Console;

use Fidry\Console\Test\OutputAssertions;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @psalm-require-implements \Humbug\PhpScoper\Console\AppTesterTestCase
 * @psalm-require-extends PHPUnit\Framework\TestCase
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
        callable ...$extraNormalizers
    ): void
    {
        OutputAssertions::assertSameOutput(
            $expectedOutput,
            $expectedStatusCode,
            $this->getAppTester(),
            [DisplayNormalizer::class, 'normalize'],
            ...$extraNormalizers,
        );
    }
}
