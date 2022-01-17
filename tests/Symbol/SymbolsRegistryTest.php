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
     * @param array<array{FullyQualified, FullyQualified}> $classes
     * @param array<array{FullyQualified, FullyQualified}> $functions
     * @param list<array{FullyQualified, FullyQualified}> $expectedRecordedClasses
     * @param list<array{FullyQualified, FullyQualified}> $expectedRecordedFunctions
     */
    public function test_it_records_functions_and_classes(
        array $classes,
        array $functions,
        array $expectedRecordedClasses,
        array $expectedRecordedFunctions,
        int $expectedCount
    ): void
    {
        $registry = new SymbolsRegistry();

        foreach ($classes as [$original, $alias]) {
            $registry->recordClass($original, $alias);
        }

        foreach ($functions as [$original, $alias]) {
            $registry->recordFunction($original, $alias);
        }

        self::assertSame(
            $expectedRecordedClasses,
            $registry->getRecordedClasses(),
        );
        self::assertSame(
            $expectedRecordedFunctions,
            $registry->getRecordedFunctions(),
        );
        self::assertSame($expectedCount, $registry->count());
    }

    public static function provideRecords(): iterable
    {
        $testCase = new FullyQualified('PHPUnit\TestCase');
        $scopedTestCase = new FullyQualified('Scoped\PHPUnit\TestCase');

        $finder = new FullyQualified('Symfony\Component\Finder\Finder');
        $scopedFinder = new FullyQualified('Scoped\Symfony\Component\Finder\Finder');

        $main = new FullyQualified('PHPUnit\main');
        $scopedMain = new FullyQualified('Scoped\PHPUnit\main');

        $dump = new FullyQualified('dump');
        $scopedDump = new FullyQualified('Scoped\dump');

        yield 'empty' => [
            [],
            [],
            [],
            [],
            0,
        ];

        yield 'nominal' => [
            [
                [$testCase, $scopedTestCase],
                [$finder, $scopedFinder],
            ],
            [
                [$main, $scopedMain],
                [$dump, $scopedDump],
            ],
            [
                ['PHPUnit\TestCase', 'Scoped\PHPUnit\TestCase'],
                ['Symfony\Component\Finder\Finder', 'Scoped\Symfony\Component\Finder\Finder'],
            ],
            [
                ['PHPUnit\main', 'Scoped\PHPUnit\main'],
                ['dump', 'Scoped\dump'],
            ],
            4,
        ];

        yield 'duplicates' => [
            [
                [$testCase, $scopedTestCase],
                [$testCase, $scopedFinder],
            ],
            [
                [$main, $scopedMain],
                [$main, $scopedDump],
            ],
            [
                ['PHPUnit\TestCase', 'Scoped\Symfony\Component\Finder\Finder'],
            ],
            [
                ['PHPUnit\main', 'Scoped\dump'],
            ],
            2,
        ];
    }
}
