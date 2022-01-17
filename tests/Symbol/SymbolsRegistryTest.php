<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Symbol;

use PhpParser\Node\Name\FullyQualified;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Symbol\SymbolsRegistry
 */
final class SymbolsRegistryTest extends TestCase
{
    /**
     * @dataProvider provideRecords
     *
     * @param array<array{FullyQualified, FullyQualified}> $functions
     * @param array<array{FullyQualified, FullyQualified}> $classes
     * @param list<array{FullyQualified, FullyQualified}> $expectedRecordedFunctions
     * @param list<array{FullyQualified, FullyQualified}> $expectedRecordedClasses
     */
    public function test_it_records_functions_and_classes(
        array $functions,
        array $classes,
        array $expectedRecordedFunctions,
        array $expectedRecordedClasses,
        int $expectedCount
    ): void
    {
        $registry = new SymbolsRegistry();

        foreach ($functions as [$original, $alias]) {
            $registry->recordFunction($original, $alias);
        }

        foreach ($classes as [$original, $alias]) {
            $registry->recordClass($original, $alias);
        }

        self::assertSame(
            $expectedRecordedFunctions,
            $registry->getRecordedFunctions(),
        );
        self::assertSame(
            $expectedRecordedClasses,
            $registry->getRecordedClasses(),
        );
        self::assertSame($expectedCount, $registry->count());
    }

    public static function provideRecords(): iterable
    {
        $main = new FullyQualified('PHPUnit\main');
        $scopedMain = new FullyQualified('Scoped\PHPUnit\main');

        $dump = new FullyQualified('dump');
        $scopedDump = new FullyQualified('Scoped\dump');

        $testCase = new FullyQualified('PHPUnit\TestCase');
        $scopedTestCase = new FullyQualified('Scoped\PHPUnit\TestCase');

        $finder = new FullyQualified('Symfony\Component\Finder\Finder');
        $scopedFinder = new FullyQualified('Scoped\Symfony\Component\Finder\Finder');

        yield 'empty' => [
            [],
            [],
            [],
            [],
            0,
        ];

        yield 'nominal' => [
            [
                [$main, $scopedMain],
                [$dump, $scopedDump],
            ],
            [
                [$testCase, $scopedTestCase],
                [$finder, $scopedFinder],
            ],
            [
                ['PHPUnit\main', 'Scoped\PHPUnit\main'],
                ['dump', 'Scoped\dump'],
            ],
            [
                ['PHPUnit\TestCase', 'Scoped\PHPUnit\TestCase'],
                ['Symfony\Component\Finder\Finder', 'Scoped\Symfony\Component\Finder\Finder'],
            ],
            4,
        ];

        yield 'duplicates' => [
            [
                [$main, $scopedMain],
                [$main, $scopedDump],
            ],
            [
                [$testCase, $scopedTestCase],
                [$testCase, $scopedFinder],
            ],
            [
                ['PHPUnit\main', 'Scoped\dump'],
            ],
            [
                ['PHPUnit\TestCase', 'Scoped\Symfony\Component\Finder\Finder'],
            ],
            2,
        ];
    }
}
