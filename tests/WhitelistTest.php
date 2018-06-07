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

namespace Humbug\PhpScoper;

use PHPUnit\Framework\TestCase;
use Reflection;
use ReflectionClass;

/**
 * @covers \Humbug\PhpScoper\Whitelist
 */
class WhitelistTest extends TestCase
{
    /**
     * @dataProvider provideWhitelists
     */
    public function test_it_can_be_created_from_a_list_of_strings(
        array $whitelist,
        array $expectedClasses,
        array $expectedNamespaces
    ) {
        $whitelistObject = Whitelist::create(...$whitelist);

        $whitelistReflection = new ReflectionClass(Whitelist::class);

        $whitelistClassReflection = $whitelistReflection->getProperty('classes');
        $whitelistClassReflection->setAccessible(true);
        $actualClasses = $whitelistClassReflection->getValue($whitelistObject);

        $whitelistNamespaceReflection = $whitelistReflection->getProperty('namespaces');
        $whitelistNamespaceReflection->setAccessible(true);
        $actualNamespaces = $whitelistNamespaceReflection->getValue($whitelistObject);

        $this->assertSame($expectedClasses, $actualClasses);
        $this->assertSame($expectedNamespaces, $actualNamespaces);
    }

    /**
     * @dataProvider provideClassWhitelists
     */
    public function test_it_can_tell_if_a_class_is_whitelisted(Whitelist $whitelist, string $class, bool $expected)
    {
        $actual = $whitelist->isClassWhitelisted($class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideNamespaceWhitelists
     */
    public function test_it_can_tell_if_a_namespace_is_whitelisted(Whitelist $whitelist, string $class, bool $expected)
    {
        $actual = $whitelist->isNamespaceWhitelisted($class);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideWhitelistToConvert
     */
    public function test_it_can_be_converted_back_into_an_array(Whitelist $whitelist, array $expected)
    {
        $actual = $whitelist->toArray();

        $this->assertSame($expected, $actual);
    }

    public function provideWhitelists()
    {
        yield [[], [], []];

        yield [['Acme\Foo'], ['Acme\Foo'], []];

        yield [['\Acme\Foo'], ['Acme\Foo'], []];

        yield [['Acme\Foo\*'], [], ['Acme\Foo']];

        yield [['\*'], [], ['']];

        yield [['*'], [], ['']];

        yield [['Acme\Foo', 'Acme\Foo\*', '\*'], ['Acme\Foo'], ['Acme\Foo', '']];
    }

    public function provideClassWhitelists()
    {
        yield [
            Whitelist::create(),
            'Acme\Foo',
            false,
        ];

        yield [
            Whitelist::create('Acme\Foo'),
            'Acme\Foo',
            true,
        ];

        yield [
            Whitelist::create('Acme\Foo'),
            'Acme\Foo\Bar',
            false,
        ];

        yield [
            Whitelist::create('Acme\Foo'),
            'Acme',
            false,
        ];

        yield [
            Whitelist::create('Acme'),
            'Acme',
            true,
        ];

        yield [
            Whitelist::create('Acme\*'),
            'Acme',
            false,
        ];
    }

    public function provideNamespaceWhitelists()
    {
        yield [
            Whitelist::create(),
            'Acme\Foo',
            false,
        ];

        yield [
            Whitelist::create('Acme\Foo\*'),
            'Acme\Foo',
            true,
        ];

        yield [
            Whitelist::create('Acme\*'),
            'Acme\Foo',
            true,
        ];

        yield [
            Whitelist::create('Acme\Foo\*'),
            'Acme\Foo\Bar',
            true,
        ];

        yield [
            Whitelist::create('\*'),
            'Acme',
            true,
        ];

        yield [
            Whitelist::create('\*'),
            'Acme\Foo',
            true,
        ];
    }

    public function provideWhitelistToConvert()
    {
        yield [
            Whitelist::create(),
            [],
        ];

        yield [
            Whitelist::create('Acme\Foo'),
            ['Acme\Foo'],
        ];

        yield [
            Whitelist::create('\Acme\Foo'),
            ['Acme\Foo'],
        ];

        yield [
            Whitelist::create('Acme\Foo\*'),
            ['Acme\Foo\*'],
        ];

        yield [
            Whitelist::create('\Acme\Foo\*'),
            ['Acme\Foo\*'],
        ];

        yield [
            Whitelist::create('*'),
            ['*'],
        ];

        yield [
            Whitelist::create('\*'),
            ['*'],
        ];

        yield [
            Whitelist::create('Acme', 'Acme\Foo', 'Acme\Foo\*', '*'),
            ['Acme', 'Acme\Foo', 'Acme\Foo\*', '*'],
        ];

        yield [
            Whitelist::create('Acme', 'Acme'),
            ['Acme'],
        ];
    }
}
