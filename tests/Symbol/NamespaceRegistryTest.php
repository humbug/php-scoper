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
 * @covers \Humbug\PhpScoper\Symbol\NamespaceRegistry
 */
class NamespaceRegistryTest extends TestCase
{
    private RegexChecker $regexChecker;

    protected function setUp(): void
    {
        $this->regexChecker = new RegexChecker();
    }

    /**
     * @dataProvider provideSymbol
     *
     * @param string[] $namespaceRegexes
     * @param string[] $namespaceNames
     */
    public function test_it_can_tell_if_a_symbol_belongs_to_a_registered_namespace(
        array $namespaceNames,
        array $namespaceRegexes,
        string $symbol,
        bool $expected
    ): void
    {
        // Sanity check
        $this->validateRegexes($namespaceRegexes);

        $registeredNamespaces = NamespaceRegistry::create(
            $namespaceNames,
            $namespaceRegexes,
        );

        $actual = $registeredNamespaces->belongsToRegisteredNamespace($symbol);

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideNamespaceSymbol
     */
    public function test_it_can_tell_if_a_namespace_is_a_registered_namespace(
        array $namespaceNames,
        array $namespaceRegexes,
        string $namespaceName,
        bool $expected
    ): void
    {
        // Sanity check
        $this->validateRegexes($namespaceRegexes);

        $registeredNamespaces = NamespaceRegistry::create(
            $namespaceNames,
            $namespaceRegexes,
        );

        $actual = $registeredNamespaces->isRegisteredNamespace($namespaceName);

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideNamesAndRegexes
     *
     * @param string[] $regexes
     * @param string[] $names
     * @param list<string> $regexes
     * @param list<string> $names
     */
    public function test_it_optimizes_the_registered_names_and_regexes(
        array $names,
        array $regexes,
        array $expectedNames,
        array $expectedRegexes
    ): void
    {
        $registry = NamespaceRegistry::create(
            $names,
            $regexes,
        );

        NamespaceRegistryAssertions::assertStateIs(
            $registry,
            $expectedNames,
            $expectedRegexes,
        );
    }

    public static function provideSymbol(): iterable
    {
        foreach (self::provideNamespaceNames() as $title => [$namespaceNames, $symbol, $expected]) {
            yield '[name only] '.$title => [
                $namespaceNames,
                [],
                $symbol,
                $expected,
            ];

            yield '[(polluted) name only] '.$title => [
                $namespaceNames,
                [],
                '\\'.$symbol,
                $expected,
            ];
        }

        foreach (self::provideNamespaceRegex() as $title => [$namespaceRegexes, $symbol, $expected]) {
            yield '[regex only] '.$title => [
                [],
                $namespaceRegexes,
                $symbol,
                $expected,
            ];

            yield '[(polluted) regex only] '.$title => [
                [],
                $namespaceRegexes,
                '\\'.$symbol,
                $expected,
            ];
        }

        foreach (self::provideNamespaceNameAndRegex() as $title => $set) {
            yield '[name & regex] '.$title => $set;
        }
    }

    private static function provideNamespaceNames(): iterable
    {
        // Global namespace
        yield 'no registered namespace; symbol belonging to global namespace' => [
            [],
            'Acme',
            false,
        ];

        yield 'no registered namespace; symbol belonging to a namespace' => [
            [],
            'Acme\Foo',
            false,
        ];

        yield 'global namespace; symbol belonging to global namespace' => [
            [''],
            'Acme',
            true,
        ];

        yield 'global namespace; symbol belonging to a namespace' => [
            [''],
            'Acme\Foo',
            true,
        ];

        yield 'one level namespace name; symbol belonging to global namespace' => [
            ['Acme'],
            'Acme',
            false,
        ];

        yield 'one level namespace name; symbol belonging to the namespace' => [
            ['Acme'],
            'Acme\Foo',
            true,
        ];

        yield 'one level namespace name; symbol belonging to the namespace (different case)' => [
            ['Acme'],
            'ACME\Foo',
            true,
        ];

        yield 'one level namespace name; symbol belonging to another namespace' => [
            ['Acme'],
            'Emca\Foo',
            false,
        ];

        yield 'one level namespace name; symbol belonging to a sub-namespace' => [
            ['Acme'],
            'Acme\Foo\Bar',
            true,
        ];

        // One level namespace namespaced symbol
        yield 'two level namespace name; symbol belonging to the parent namespace' => [
            ['PHPUnit\Framework'],
            'PHPUnit\TestCase',
            false,
        ];

        yield 'two level namespace name; symbol belonging to the namespace' => [
            ['PHPUnit\Framework'],
            'PHPUnit\Framework\TestCase',
            true,
        ];

        yield 'two level namespace name; symbol belonging to a sub-namespace' => [
            ['PHPUnit\Framework'],
            'PHPUnit\Framework\Test\TestCase',
            true,
        ];

        yield 'two level namespace name; symbol belonging to a sub-namespace (different case)' => [
            ['PHPUnit\Framework'],
            'PHPUNIT\FRAMEWORK\TEST\TestCase',
            true,
        ];

        // Two level namespace namespaced symbol
        yield 'three level namespace name; symbol belonging to the parent namespace' => [
            ['PHPUnit\TestCase\Framework'],
            'PHPUnit\TestCase\TestCase',
            false,
        ];

        yield 'three level namespace name; symbol belonging to the namespace' => [
            ['PHPUnit\Framework\TestCase'],
            'PHPUnit\Framework\TestCase\TestCase',
            true,
        ];

        yield 'three level namespace name; symbol belonging to a sub-namespace' => [
            ['PHPUnit\Framework\TestCase'],
            'PHPUnit\Framework\TestCase\Test\TestCase',
            true,
        ];

        yield 'three level namespace name; symbol belonging to a sub-namespace (different case)' => [
            ['PHPUnit\Framework\TestCase'],
            'PHPUNIT\FRAMEWORK\TESTCASE\TEST\TestCase',
            true,
        ];

        // Misc
        yield 'multiple names: at least one matching' => [
            [
                'Acme\Framework',
                'PHPUnit\Framework',
            ],
            'PHPUNIT\FRAMEWORK\TEST\TestCase',
            true,
        ];
    }

    private static function provideNamespaceRegex(): iterable
    {
        yield 'no registered namespace; symbol belonging to global namespace' => [
            [],
            'Acme',
            false,
        ];

        yield 'no registered namespace; symbol belonging to a namespace' => [
            [],
            'Acme\Foo',
            false,
        ];

        yield 'global namespace; symbol belonging to global namespace' => [
            ['/^$/'],
            'Acme',
            true,
        ];

        yield 'global namespace; symbol belonging to a namespace' => [
            ['/^$/'],
            'Acme\Foo',
            false,
        ];

        yield 'one level namespace name; symbol belonging to global namespace' => [
            ['/^Acme$/'],
            'Acme',
            false,
        ];

        yield 'one level namespace name; symbol belonging to the namespace' => [
            ['/^Acme$/'],
            'Acme\Foo',
            true,
        ];

        yield 'one level namespace name; symbol belonging to the namespace (different case)' => [
            ['/^Acme$/'],
            'ACME\Foo',
            false,
        ];

        yield 'one level namespace name; symbol belonging to the namespace (different case; case insensitive flag)' => [
            ['/^Acme$/i'],
            'ACME\Foo',
            true,
        ];

        yield 'one level namespace name; symbol belonging to another namespace' => [
            ['/^Acme$/'],
            'Emca\Foo',
            false,
        ];

        yield 'one level namespace name; symbol belonging to a sub-namespace' => [
            ['/^Acme$/'],
            'Acme\Foo\Bar',
            false,
        ];

        yield 'one level namespace name allowing sub-namespaces; symbol belonging to a sub-namespace' => [
            ['/^Acme\\\\.*$/'],
            'Acme\Foo\Bar',
            true,
        ];
    }

    private static function provideNamespaceNameAndRegex(): iterable
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
            'acme\Foo',
            true,
        ];

        yield 'matches the regex but not the name' => [
            ['ecma'],
            ['/^Acme$/'],
            'Acme\Foo',
            true,
        ];

        yield 'matches both' => [
            ['Acme$'],
            ['/^Acme$/i'],
            'Acme\Foo',
            true,
        ];
    }

    public static function provideNamespaceSymbol(): iterable
    {
        yield 'namespace matches regex' => [
            [],
            ['/^Acme/'],
            'Acme',
            true,
        ];

        yield '(unormalized) namespace matches regex' => [
            [],
            ['/^Acme/'],
            '\Acme',
            true,
        ];

        // We are not interested in much more tests here as the targeted code is
        // mostly ::covered by test_it_can_tell_if_a_symbol_belongs_to_a_registered_namespace()
    }

    public static function provideNamesAndRegexes(): iterable
    {
        yield 'nominal' => [
            ['Acme\Foo', 'Acme\Bar'],
            ['/^Acme$/', '/^Ecma/'],
            ['acme\bar', 'acme\foo'],
            ['/^Acme$/', '/^Ecma/'],
        ];

        yield 'duplicates' => [
            [
                'Acme\Foo',
                'Acme\Foo',
                'ACME\FOO',
                '\Acme\Foo',
                'Acme\Foo\\',
            ],
            [
                '/^Acme$/',
                '/^Acme$/',
            ],
            ['acme\foo'],
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
