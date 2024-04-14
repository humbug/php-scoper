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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(SymbolsRegistry::class)]
final class SymbolsRegistryTest extends TestCase
{
    /**
     * @param array<array{FullyQualified, FullyQualified}> $functions
     * @param array<array{FullyQualified, FullyQualified}> $classes
     * @param list<array{FullyQualified, FullyQualified}>  $expectedRecordedFunctions
     * @param list<array{FullyQualified, FullyQualified}>  $expectedRecordedClasses
     */
    #[DataProvider('provideRecords')]
    public function test_it_records_functions_and_classes(
        array $functions,
        array $classes,
        array $expectedRecordedFunctions,
        array $expectedRecordedClasses,
        int $expectedCount
    ): void {
        $registry = SymbolsRegistry::create($functions, $classes);

        self::assertStateIs(
            $registry,
            $expectedRecordedFunctions,
            $expectedRecordedClasses,
            $expectedCount,
        );
    }

    /**
     * @param array<array{FullyQualified, FullyQualified}> $functions
     * @param array<array{FullyQualified, FullyQualified}> $classes
     */
    #[DataProvider('provideRecords')]
    public function test_it_can_be_serialized_and_unserialized(
        array $functions,
        array $classes,
    ): void {
        $registry = SymbolsRegistry::create($functions, $classes);

        $unserializedRegistry = SymbolsRegistry::unserialize(
            $registry->serialize(),
        );

        self::assertEquals($unserializedRegistry, $registry);
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

    #[DataProvider('provideRegistryToMerge')]
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

        $finder = new FullyQualified('Symfony\Component\Finder\Finder');
        $scopedFinder = new FullyQualified('Scoped\Symfony\Component\Finder\Finder');

        yield 'empty' => [
            new SymbolsRegistry(),
            new SymbolsRegistry(),
            [],
            [],
            0,
        ];

        yield 'elements in the source' => [
            SymbolsRegistry::create(
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
            SymbolsRegistry::create(
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
            SymbolsRegistry::create(
                [
                    [$main, $scopedMain],
                ],
                [
                    [$testCase, $scopedTestCase],
                ],
            ),
            SymbolsRegistry::create(
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
                ['Symfony\Component\Finder\Finder', 'Scoped\Symfony\Component\Finder\Finder'],
                ['PHPUnit\TestCase', 'Scoped\PHPUnit\TestCase'],
            ],
            4,
        ];

        yield 'elements on both sides with duplicates entries from the target' => [
            SymbolsRegistry::create(
                [
                    [$main, $scopedMain],
                ],
                [
                    [$testCase, $scopedTestCase],
                ],
            ),
            SymbolsRegistry::create(
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
                ['Symfony\Component\Finder\Finder', 'Scoped\Symfony\Component\Finder\Finder'],
                ['PHPUnit\TestCase', 'Scoped\PHPUnit\TestCase'],
            ],
            4,
        ];

        yield 'elements on both sides with duplicates entries from the source' => [
            SymbolsRegistry::create(
                [
                    [$main, $scopedMain],
                    [$dump, $scopedDump],
                ],
                [
                    [$testCase, $scopedTestCase],
                    [$finder, $scopedFinder],
                ],
            ),
            SymbolsRegistry::create(
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
                ['Symfony\Component\Finder\Finder', 'Scoped\Symfony\Component\Finder\Finder'],
                ['PHPUnit\TestCase', 'Scoped\PHPUnit\TestCase'],
            ],
            4,
        ];
    }

    /**
     * @param SymbolsRegistry[] $sources
     */
    #[DataProvider('provideRegistriesToMerge')]
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

        $finder = new FullyQualified('Symfony\Component\Finder\Finder');
        $scopedFinder = new FullyQualified('Scoped\Symfony\Component\Finder\Finder');

        $fileSystem = new FullyQualified('Symfony\Component\Filesystem\Filesystem');
        $scopedFileSystem = new FullyQualified('Scoped\Symfony\Component\Filesystem\Filesystem');

        yield 'empty' => [
            [],
            [],
            [],
            0,
        ];

        yield 'nominal' => [
            [
                SymbolsRegistry::create(
                    [[$main, $scopedMain]],
                    [[$testCase, $scopedTestCase]],
                ),
                SymbolsRegistry::create(
                    [[$dump, $scopedDump]],
                    [[$finder, $scopedFinder]],
                ),
                SymbolsRegistry::create(
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
                ['Symfony\Component\Filesystem\Filesystem', 'Scoped\Symfony\Component\Filesystem\Filesystem'],
                ['Symfony\Component\Finder\Finder', 'Scoped\Symfony\Component\Finder\Finder'],
                ['PHPUnit\TestCase', 'Scoped\PHPUnit\TestCase'],
            ],
            6,
        ];
    }

    public function test_it_exposes_recorded_classes(): void
    {
        $registry = SymbolsRegistry::create(
            [[new FullyQualified('foo'), new FullyQualified('Humbug\foo')]],
            [[new FullyQualified('Bar'), new FullyQualified('Humbug\Bar')]],
        );

        self::assertNull($registry->getRecordedClass('foo'));
        self::assertNull($registry->getRecordedClass('bar'));
        self::assertNull($registry->getRecordedClass('Humbug\Bar'));
        self::assertEquals(
            ['Bar', 'Humbug\Bar'],
            $registry->getRecordedClass('Bar'),
        );
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
}
