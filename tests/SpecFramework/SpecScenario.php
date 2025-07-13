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

namespace Humbug\PhpScoper\SpecFramework;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PhpParser\PhpVersion;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\SkippedWithMessageException;
use PHPUnit\Framework\TestCase;
use Throwable;
use function usort;
use const PHP_VERSION_ID;

final readonly class SpecScenario
{
    public function __construct(
        public ?int $minPhpVersion,
        public ?int $maxPhpVersion,
        public ?int $phpVersionUsed,
        public string $file,
        public string $title,
        public string $inputCode,
        public string $prefix,
        public SymbolsConfiguration $symbolsConfiguration,
        public ?string $expectedCode,
        public array $expectedRegisteredClasses,
        public array $expectedRegisteredFunctions,
    ) {
    }

    public function getPhpParserVersion(): ?PhpVersion
    {
        $phpVersionId = $this->phpVersionUsed;

        if (null === $phpVersionId) {
            return null;
        }

        $minorRemainder = $phpVersionId % 1000;
        $minor = $minorRemainder / 100;
        $major = ($phpVersionId - $minorRemainder) / 10_000;

        return PhpVersion::fromComponents($major, $minor);
    }

    public function checkPHPVersionRequirements(?int $phpVersionIdUsed): void
    {
        $phpVersionIdUsed ??= PHP_VERSION_ID;
        $minPhpVersion = $this->minPhpVersion;
        $maxPhpVersion = $this->maxPhpVersion;

        if (null !== $minPhpVersion && $minPhpVersion > $phpVersionIdUsed) {
            throw new SkippedWithMessageException(
                sprintf(
                    'Min PHP version not matched for spec "%s".',
                    $this->title,
                ),
            );
        }

        if (null !== $maxPhpVersion && $maxPhpVersion <= $phpVersionIdUsed) {
            throw new SkippedWithMessageException(
                sprintf(
                    'Max PHP version not matched for spec "%s".',
                    $this->title,
                ),
            );
        }
    }

    public function failIfExpectedFailure(Assert $assert): void
    {
        if (null === $this->expectedCode) {
            $assert->fail('Expected exception to be thrown.');
        }
    }

    public function assertExpectedFailure(TestCase $assert, Throwable $failure): void
    {
        if (null !== $this->expectedCode) {
            throw $failure;
        }

        $assert->addToAssertionCount(1);
    }

    public function assertExpectedResult(
        Assert $assert,
        SymbolsRegistry $symbolsRegistry,
        ?string $actualCode,
    ): void {
        $specMessage = SpecPrinter::createSpecMessage(
            $this,
            $symbolsRegistry,
            $actualCode,
        );

        $assert->assertSame($this->expectedCode, $actualCode, $specMessage);

        $actualRecordedExposedClasses = $symbolsRegistry->getRecordedClasses();

        self::assertSameRecordedSymbols(
            $assert,
            $this->expectedRegisteredClasses,
            $actualRecordedExposedClasses,
            $specMessage,
        );

        $actualRecordedExposedFunctions = $symbolsRegistry->getRecordedFunctions();

        self::assertSameRecordedSymbols(
            $assert,
            $this->expectedRegisteredFunctions,
            $actualRecordedExposedFunctions,
            $specMessage,
        );
    }

    /**
     * @param string[][] $expected
     * @param string[][] $actual
     */
    private static function assertSameRecordedSymbols(
        Assert $assert,
        array $expected,
        array $actual,
        string $message,
    ): void {
        $sort = static fn (array $a, array $b) => $a[0] <=> $b[0];

        usort($expected, $sort);
        usort($actual, $sort);

        $assert->assertSame($expected, $actual, $message);
    }
}
