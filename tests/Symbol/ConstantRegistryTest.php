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
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Symbol\SymbolRegistry
 */
class ConstantRegistryTest extends TestCase
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

        $registry = SymbolRegistry::createForConstants(
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

    private function validateRegexes(array $regexes): void
    {
        foreach ($regexes as $regex) {
            self::assertNull($this->regexChecker->validateRegex($regex));
        }
    }
}
