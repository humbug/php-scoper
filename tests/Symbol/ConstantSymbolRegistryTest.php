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

use Humbug\PhpScoper\Configuration\RegexChecker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(\Humbug\PhpScoper\Symbol\SymbolRegistry::class)]
class ConstantSymbolRegistryTest extends TestCase
{
    private RegexChecker $regexChecker;

    protected function setUp(): void
    {
        $this->regexChecker = new RegexChecker();
    }

    /**
     * @param string[] $regexes
     * @param string[] $names
     */
    #[DataProvider('provideSymbols')]
    public function test_it_can_tell_if_it_matches_the_symbol(
        array $names,
        array $regexes,
        string $symbol,
        bool $expected
    ): void {
        // Sanity check
        $this->validateRegexes($regexes);

        $registry = SymbolRegistry::createForConstants(
            $names,
            $regexes,
        );

        $actual = $registry->matches($symbol);

        self::assertSame($expected, $actual);
    }

    /**
     * @param string[]     $regexes
     * @param string[]     $names
     * @param list<string> $regexes
     * @param list<string> $names
     */
    #[DataProvider('provideNamesAndRegexes')]
    public function test_it_optimizes_the_registered_names_and_regexes(
        array $names,
        array $regexes,
        array $expectedNames,
        array $expectedRegexes
    ): void {
        $registry = SymbolRegistry::createForConstants(
            $names,
            $regexes,
        );

        SymbolRegistryAssertions::assertStateIs(
            $registry,
            $expectedNames,
            $expectedRegexes,
        );
    }

    public function test_it_can_create_an_augmented_instance(): void
    {
        $registry = SymbolRegistry::createForConstants(
            ['Acme\Foo'],
            ['/^Acme\\\\Foo$/'],
        );

        $augmentedRegistry = $registry->merge(
            SymbolRegistry::createForConstants(
                ['Acme\Bar'],
                ['/^Acme\\\\Bar/'],
            ),
        );

        SymbolRegistryAssertions::assertStateIs(
            $registry,
            ['acme\Foo'],
            ['/^Acme\\\\Foo$/'],
        );

        SymbolRegistryAssertions::assertStateIs(
            $augmentedRegistry,
            [
                'acme\Foo',
                'acme\Bar',
            ],
            [
                '/^Acme\\\\Foo$/',
                '/^Acme\\\\Bar/',
            ],
        );
    }

    public static function provideSymbols(): iterable
    {
        foreach (self::provideNames() as $title => [$names, $symbol, $expected]) {
            yield '[name only] '.$title => [
                $names,
                [],
                $symbol,
                $expected,
            ];

            yield '[(polluted) name only] '.$title => [
                $names,
                [],
                $symbol,
                $expected,
            ];
        }

        foreach (self::provideRegex() as $title => [$regexes, $symbol, $expected]) {
            yield '[regex only] '.$title => [
                [],
                $regexes,
                $symbol,
                $expected,
            ];

            yield '[(polluted) regex only] '.$title => [
                [],
                $regexes,
                $symbol,
                $expected,
            ];
        }

        foreach (self::provideNameAndRegex() as $title => $set) {
            yield '[name & regex] '.$title => $set;
        }
    }

    private static function provideNames(): iterable
    {
        yield 'no name registered' => [
            [],
            'Box',
            false,
        ];

        yield 'name registered; exact match' => [
            ['BOX'],
            'BOX',
            true,
        ];

        yield 'name registered; different case' => [
            ['Box'],
            'BOX',
            false,
        ];

        yield 'name registered; different name' => [
            ['Humbug\BOX'],
            'TestCase',
            false,
        ];

        yield 'name registered; name from sub-namespace' => [
            ['Box'],
            'Box\PARALLEL',
            false,
        ];

        yield 'namespaced name registered; exact match' => [
            ['Humbug\BOX'],
            'Humbug\BOX',
            true,
        ];

        yield 'namespaced name registered; different namespace case' => [
            ['Humbug\BOX'],
            'HUMBUG\BOX',
            true,
        ];

        yield 'namespaced name registered; different case' => [
            ['Humbug\BOX'],
            'Humbug\Box',
            false,
        ];

        yield 'namespaced name registered; different name' => [
            ['Humbug\BOX'],
            'Humbug\PARALLEL',
            false,
        ];

        yield 'namespaced name registered; name from sub-namespace' => [
            ['Humbug\BOX'],
            'Humbug\BOX\PARALLEL',
            false,
        ];

        yield 'namespaced name registered; name from parent namespace' => [
            ['Humbug\BOX'],
            'Humbug',
            false,
        ];
    }

    private static function provideRegex(): iterable
    {
        yield 'no name registered' => [
            [],
            'TestCase',
            false,
        ];

        yield 'name registered; matching (exact match)' => [
            ['/^Acme$/'],
            'Acme',
            true,
        ];

        yield 'name registered; different' => [
            ['/^Acme$/'],
            'TestCase',
            false,
        ];

        yield 'name registered; matching (different case, case-sensitive comparison)' => [
            ['/^Acme$/'],
            'acme',
            false,
        ];

        yield 'name registered; matching (different case, case-insensitive comparison)' => [
            ['/^Acme$/i'],
            'ACME',
            true,
        ];

        yield 'namespaced name; matching' => [
            ['/^Acme\\\\Foo$/'],
            'Acme\Foo',
            true,
        ];
    }

    private static function provideNameAndRegex(): iterable
    {
        yield 'empty' => [
            [],
            [],
            'Acme',
            false,
        ];

        yield 'matches the name but not the regex' => [
            ['acme'],
            ['/^Acme$/'],
            'acme',
            true,
        ];

        yield 'matches the regex but not the name' => [
            ['ecma'],
            ['/^Acme$/'],
            'Acme',
            true,
        ];

        yield 'matches both' => [
            ['Acme$'],
            ['/^Acme$/i'],
            'Acme',
            true,
        ];
    }

    public static function provideNamesAndRegexes(): iterable
    {
        yield 'nominal' => [
            ['Acme\Foo', 'Acme\Bar'],
            ['/^Acme$/', '/^Ecma/'],
            ['acme\Foo', 'acme\Bar'],
            ['/^Acme$/', '/^Ecma/'],
        ];

        yield 'duplicates' => [
            [
                'Acme\Foo',
                'Acme\Foo',
                'ACME\FOO',
                'ACME\FOO',
                '\Acme\Foo',
                'Acme\Foo\\',
            ],
            [
                '/^Acme$/',
                '/^Acme$/',
            ],
            ['acme\Foo', 'acme\FOO'],
            ['/^Acme$/'],
        ];
    }

    private function validateRegexes(array $regexes): void
    {
        foreach ($regexes as $regex) {
            self::assertNull($this->regexChecker->validateRegex($regex));
        }
    }
}
