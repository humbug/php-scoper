<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Console;

use Fidry\Console\DisplayNormalizer as FidryDisplayNormalizer;
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
     * @param null|callable(string):string $extraNormalization
     */
    private function assertExpectedOutput(
        string $expectedOutput,
        int $expectedStatusCode,
        ?callable $extraNormalization = null
    ): void
    {
        $appTester = $this->getAppTester();

        $actual = $this->getNormalizeDisplay(
            $appTester->getDisplay(true),
            $extraNormalization,
        );

        self::assertSame($expectedOutput, $actual);
        self::assertSame($expectedStatusCode, $appTester->getStatusCode());
    }

    private function getNormalizeDisplay(
        string $display,
        ?callable $extraNormalization = null
    ): string
    {
        $extraNormalization = $extraNormalization ?? static fn (string $display) => $display;

        $display = DisplayNormalizer::normalizeDirectorySeparators($display);
        $display = DisplayNormalizer::normalizeProgressBar($display);
        $display = FidryDisplayNormalizer::removeTrailingSpaces($display);

        return $extraNormalization($display);
    }
}
