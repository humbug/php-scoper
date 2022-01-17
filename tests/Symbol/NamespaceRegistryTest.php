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

use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use PhpParser\Node\Name\FullyQualified;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Symbol\NamespaceRegistry
 */
class NamespaceRegistryTest extends TestCase
{
    /**
     * @dataProvider provideNamespacedSymbol
     *
     * @param string[] $namespaceRegexes
     * @param string[] $namespaceNames
     */
    public function test_it_can_tell_if_a_symbol_belongs_to_a_registered_namespace(
        array $namespaceRegexes,
        array $namespaceNames,
        string $symbol,
        bool $expected
    ): void
    {
        $registeredNamespaces = new NamespaceRegistry(
            $namespaceRegexes,
            $namespaceNames,
        );

        $actual = $registeredNamespaces->belongsToRegisteredNamespace($symbol);

        self::assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideNamespaces
     *
     * @param string[] $namespaceRegexes
     * @param string[] $namespaceNames
     */
    public function test_it_can_tell_if_it_is_a_registered_namespace(
        array $namespaceRegexes,
        array $namespaceNames,
        string $namespace,
        bool $expected
    ): void
    {
        $registeredNamespaces = new NamespaceRegistry(
            $namespaceRegexes,
            $namespaceNames,
        );

        $actual = $registeredNamespaces->isRegisteredNamespace($namespace);

        self::assertSame($expected, $actual);
    }

    public static function provideNamespacedSymbol(): iterable
    {
        yield 'no registered namespace' => [
            [],
            [],
            'Acme\Foo',
            false,
        ];

        yield 'single level namespace – matching' => [
            [],
            ['Acme'],
            'Acme',
            true,
        ];

        yield 'belongs to the namespace 2 (name)' => [
            [],
            ['Acme\Foo'],
            'Acme\Foo\Bar',
            true,
        ];

        yield 'belongs to the namespace 3 (name)' => [
            [],
            ['Acme'],
            'Acme\Foo\Bar',
            true,
        ];
    }

    public static function provideNamespaces(): iterable
    {
        yield [
            [],
            [],
            '',
            true,
        ];
    }
}
