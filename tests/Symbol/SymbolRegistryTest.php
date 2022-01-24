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

use Humbug\PhpScoper\RegexChecker;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Symbol\SymbolRegistry
 */
class SymbolRegistryTest extends TestCase
{
    private RegexChecker $regexChecker;

    protected function setUp(): void
    {
        $this->regexChecker = new RegexChecker();
    }

    /**
     * @dataProvider provideSymbols
     *
     * @param string[] $regexes
     * @param string[] $names
     */
    public function test_it_can_tell_if_it_matches_the_symbol(
        array $names,
        array $regexes,
        string $symbol,
        bool $expected
    ): void
    {
        // Sanity check
        $this->validateRegexes($regexes);

        $registry = SymbolRegistry::create(
            $names,
            $regexes,
        );

        $actual = $registry->matches($symbol);

        self::assertSame($expected, $actual);
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
        }

        foreach (self::provideRegex() as $title => [$regexes, $symbol, $expected]) {
            yield '[regex only] '.$title => [
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
            'Pest',
            false,
        ];

        yield 'name registered; exact match' => [
            ['Pest'],
            'Pest',
            true,
        ];

        yield 'name registered; different case' => [
            ['Pest'],
            'PEST',
            true,
        ];

        yield 'name registered; different name' => [
            ['Pest'],
            'TestCase',
            false,
        ];

        yield 'name registered; name from sub-namespace' => [
            ['Pest'],
            'Pest\TestCase',
            false,
        ];

        yield 'namespaced name registered; exact match' => [
            ['PHPUnit\TestCase'],
            'PHPUnit\TestCase',
            true,
        ];

        yield 'namespaced name registered; different case' => [
            ['PHPUnit\TestCase'],
            'PHPUNIT\TESTCASE',
            true,
        ];

        yield 'namespaced name registered; different name' => [
            ['PHPUnit\TestCase'],
            'PHPUnit\Framework',
            false,
        ];

        yield 'namespaced name registered; name from sub-namespace' => [
            ['PHPUnit\TestCase'],
            'PHPUnit\Framework\TestCase',
            false,
        ];

        yield 'namespaced name registered; name from parent namespace' => [
            ['PHPUnit\TestCase'],
            'PHPUnit',
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

    private function validateRegexes(array $regexes): void
    {
        foreach ($regexes as $regex) {
            self::assertNull($this->regexChecker->validateRegex($regex));
        }
    }
}
