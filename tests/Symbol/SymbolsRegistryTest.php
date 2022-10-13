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

namespace Humbug\PhpScoper\Symbol;

use Humbug\PhpScoper\PhpScoperAssertions;
use PhpParser\Node\Name\FullyQualified;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * @covers \Humbug\PhpScoper\Symbol\SymbolsRegistry
 *
 * @internal
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
    ): void {
        $registry = self::createRegistry($functions, $classes);

        self::assertStateIs(
            $registry,
            $expectedRecordedFunctions,
            $expectedRecordedClasses,
            $expectedCount,
        );
    }

    public static function provideRecords(): iterable
    {
        $main = new FullyQualified('PHPUnit\main');
        $scopedMain = new FullyQualified('Scoped\PHPUnit\main');

        $dump = new FullyQualified('dump');
        $scopedDump = new FullyQualified('Scoped\dump');

        $testCase = new FullyQualified('PHPUnit\TestCase');
        $scopedTestCase = new FullyQualified('Scoped\PHPUnit\TestCase');

        $finder = new FullyQualified(Finder::class);
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
                [Finder::class, 'Scoped\Symfony\Component\Finder\Finder'],
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

    /**
     * @dataProvider provideRegistryToMerge
     */
    public function test_it_can_merge_two_registries_together(
        SymbolsRegistry $source,
        SymbolsRegistry $target,
        array $expectedRecordedFunctions,
        array $expectedRecordedClasses,
        int $expectedCount
    ): void {
        $originalSource = clone $source;

        $target->merge($source);

        self::assertEquals($originalSource, $source);

        self::assertStateIs(
            $target,
            $expectedRecordedFunctions,
            $expectedRecordedClasses,
            $expectedCount,
        );
    }

    public static function provideRegistryToMerge(): iterable
    {
        $main = new FullyQualified('PHPUnit\main');
        $scopedMain = new FullyQualified('Scoped\PHPUnit\main');

        $dump = new FullyQualified('dump');
        $scopedDump = new FullyQualified('Scoped\dump');

        $testCase = new FullyQualified('PHPUnit\TestCase');
        $scopedTestCase = new FullyQualified('Scoped\PHPUnit\TestCase');

        $finder = new FullyQualified(Finder::class);
        $scopedFinder = new FullyQualified('Scoped\Symfony\Component\Finder\Finder');

        yield 'empty' => [
            new SymbolsRegistry(),
            new SymbolsRegistry(),
            [],
            [],
            0,
        ];

        yield 'elements in the source' => [
            self::createRegistry(
                [
                    [$main, $scopedMain],
                ],
                [
                    [$testCase, $scopedTestCase],
                ],
            ),
            new SymbolsRegistry(),
            [
                ['PHPUnit\main', 'Scoped\PHPUnit\main'],
            ],
            [
                ['PHPUnit\TestCase', 'Scoped\PHPUnit\TestCase'],
            ],
            2,
        ];

        yield 'elements in the target' => [
            new SymbolsRegistry(),
            self::createRegistry(
                [
                    [$main, $scopedMain],
                ],
                [
                    [$testCase, $scopedTestCase],
                ],
            ),
            [
                ['PHPUnit\main', 'Scoped\PHPUnit\main'],
            ],
            [
                ['PHPUnit\TestCase', 'Scoped\PHPUnit\TestCase'],
            ],
            2,
        ];

        yield 'elements on both sides' => [
            self::createRegistry(
                [
                    [$main, $scopedMain],
                ],
                [
                    [$testCase, $scopedTestCase],
                ],
            ),
            self::createRegistry(
                [
                    [$dump, $scopedDump],
                ],
                [
                    [$finder, $scopedFinder],
                ],
            ),
            [
                ['dump', 'Scoped\dump'],
                ['PHPUnit\main', 'Scoped\PHPUnit\main'],
            ],
            [
                [Finder::class, 'Scoped\Symfony\Component\Finder\Finder'],
                ['PHPUnit\TestCase', 'Scoped\PHPUnit\TestCase'],
            ],
            4,
        ];

        yield 'elements on both sides with duplicates entries from the target' => [
            self::createRegistry(
                [
                    [$main, $scopedMain],
                ],
                [
                    [$testCase, $scopedTestCase],
                ],
            ),
            self::createRegistry(
                [
                    [$main, $scopedMain],
                    [$dump, $scopedDump],
                ],
                [
                    [$testCase, $scopedTestCase],
                    [$finder, $scopedFinder],
                ],
            ),
            [
                ['dump', 'Scoped\dump'],
                ['PHPUnit\main', 'Scoped\PHPUnit\main'],
            ],
            [
                [Finder::class, 'Scoped\Symfony\Component\Finder\Finder'],
                ['PHPUnit\TestCase', 'Scoped\PHPUnit\TestCase'],
            ],
            4,
        ];

        yield 'elements on both sides with duplicates entries from the source' => [
            self::createRegistry(
                [
                    [$main, $scopedMain],
                    [$dump, $scopedDump],
                ],
                [
                    [$testCase, $scopedTestCase],
                    [$finder, $scopedFinder],
                ],
            ),
            self::createRegistry(
                [
                    [$dump, $scopedDump],
                ],
                [
                    [$finder, $scopedFinder],
                ],
            ),
            [
                ['dump', 'Scoped\dump'],
                ['PHPUnit\main', 'Scoped\PHPUnit\main'],
            ],
            [
                [Finder::class, 'Scoped\Symfony\Component\Finder\Finder'],
                ['PHPUnit\TestCase', 'Scoped\PHPUnit\TestCase'],
            ],
            4,
        ];
    }

    /**
     * @dataProvider provideRegistriesToMerge
     *
     * @param SymbolsRegistry[] $sources
     */
    public function test_it_can_merge_registries_together(
        array $sources,
        array $expectedRecordedFunctions,
        array $expectedRecordedClasses,
        int $expectedCount
    ): void {
        $symbolRegistry = SymbolsRegistry::createFromRegistries($sources);

        self::assertStateIs(
            $symbolRegistry,
            $expectedRecordedFunctions,
            $expectedRecordedClasses,
            $expectedCount,
        );
    }

    public static function provideRegistriesToMerge(): iterable
    {
        $main = new FullyQualified('PHPUnit\main');
        $scopedMain = new FullyQualified('Scoped\PHPUnit\main');

        $dump = new FullyQualified('dump');
        $scopedDump = new FullyQualified('Scoped\dump');

        $dd = new FullyQualified('dd');
        $scopedDd = new FullyQualified('Scoped\dd');

        $testCase = new FullyQualified('PHPUnit\TestCase');
        $scopedTestCase = new FullyQualified('Scoped\PHPUnit\TestCase');

        $finder = new FullyQualified(Finder::class);
        $scopedFinder = new FullyQualified('Scoped\Symfony\Component\Finder\Finder');

        $fileSystem = new FullyQualified(Filesystem::class);
        $scopedFileSystem = new FullyQualified('Scoped\Symfony\Component\Filesystem\Filesystem');

        yield 'empty' => [
            [],
            [],
            [],
            0,
        ];

        yield 'nominal' => [
            [
                self::createRegistry(
                    [[$main, $scopedMain]],
                    [[$testCase, $scopedTestCase]],
                ),
                self::createRegistry(
                    [[$dump, $scopedDump]],
                    [[$finder, $scopedFinder]],
                ),
                self::createRegistry(
                    [[$dd, $scopedDd]],
                    [[$fileSystem, $scopedFileSystem]],
                ),
            ],
            [
                ['dd', 'Scoped\dd'],
                ['dump', 'Scoped\dump'],
                ['PHPUnit\main', 'Scoped\PHPUnit\main'],
            ],
            [
                [Filesystem::class, 'Scoped\Symfony\Component\Filesystem\Filesystem'],
                [Finder::class, 'Scoped\Symfony\Component\Finder\Finder'],
                ['PHPUnit\TestCase', 'Scoped\PHPUnit\TestCase'],
            ],
            6,
        ];
    }

    private static function assertStateIs(
        SymbolsRegistry $symbolsRegistry,
        array $expectedRecordedFunctions,
        array $expectedRecordedClasses,
        int $expectedCount
    ): void {
        PhpScoperAssertions::assertListEqualsCanonicalizing(
            $expectedRecordedFunctions,
            $symbolsRegistry->getRecordedFunctions(),
        );
        PhpScoperAssertions::assertListEqualsCanonicalizing(
            $expectedRecordedClasses,
            $symbolsRegistry->getRecordedClasses(),
        );
        self::assertCount($expectedCount, $symbolsRegistry);
    }

    private static function createRegistry(array $functions, array $classes): SymbolsRegistry
    {
        $registry = new SymbolsRegistry();

        foreach ($functions as [$original, $alias]) {
            $registry->recordFunction($original, $alias);
        }

        foreach ($classes as [$original, $alias]) {
            $registry->recordClass($original, $alias);
        }

        return $registry;
    }
}
