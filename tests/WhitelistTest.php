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
        array $expectedNamespaces,
        array $expectedClasses,
        array $expectedConstants
    ) {
        $whitelistObject = Whitelist::create(true,true, ...$whitelist);

        $whitelistReflection = new ReflectionClass(Whitelist::class);

        $actualClasses = $whitelistObject->getClassWhitelistArray();

        $whitelistNamespaceReflection = $whitelistReflection->getProperty('namespaces');
        $whitelistNamespaceReflection->setAccessible(true);
        $actualNamespaces = $whitelistNamespaceReflection->getValue($whitelistObject);

        $whitelistConstantReflection = $whitelistReflection->getProperty('constants');
        $whitelistConstantReflection->setAccessible(true);
        $actualConstants = $whitelistConstantReflection->getValue($whitelistObject);

        $this->assertTrue($whitelistObject->whitelistGlobalConstants());
        $this->assertSame($expectedNamespaces, $actualNamespaces);
        $this->assertSame($expectedClasses, $actualClasses);
        $this->assertSame($expectedConstants, $actualConstants);

        $whitelistObject = Whitelist::create(false, false, ...$whitelist);

        $this->assertFalse($whitelistObject->whitelistGlobalConstants());
        $this->assertSame($expectedClasses, $actualClasses);
        $this->assertSame($expectedNamespaces, $actualNamespaces);
        $this->assertSame($expectedConstants, $actualConstants);
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
        yield [[], [], [], []];

        yield [['Acme\Foo'], [], ['Acme\Foo'], ['acme\Foo']];

        yield [['Acme\Foo\*'], ['acme\foo'], [], []];

        yield [['\*'], [''], [], []];

        yield [['*'], [''], [], []];

        yield [
            ['Acme\Foo', 'Acme\Foo\*', '\*'],
            ['acme\foo', ''],
            ['Acme\Foo'],
            ['acme\Foo'],
        ];
    }

    public function provideClassWhitelists()
    {
        yield [
            Whitelist::create(true, true),
            'Acme\Foo',
            false,
        ];

        yield [
            Whitelist::create(true, true,'Acme\Foo'),
            'Acme\Foo',
            true,
        ];

        yield [
            Whitelist::create(true, true,'Acme\Foo'),
            'Acme\Foo\Bar',
            false,
        ];

        yield [
            Whitelist::create(true, true,'Acme\Foo'),
            'Acme',
            false,
        ];

        yield [
            Whitelist::create(true, true,'Acme'),
            'Acme',
            true,
        ];

        yield [
            Whitelist::create(true, true,'Acme\*'),
            'Acme',
            false,
        ];
    }

    public function provideNamespaceWhitelists()
    {
        yield [
            Whitelist::create(true, true),
            'Acme\Foo',
            false,
        ];

        yield [
            Whitelist::create(true, true,'Acme\Foo\*'),
            'Acme\Foo',
            true,
        ];

        yield [
            Whitelist::create(true, true,'Acme\Foo\*'),
            'acme\foo',
            true,
        ];

        yield [
            Whitelist::create(true, true,'Acme\*'),
            'Acme\Foo',
            true,
        ];

        yield [
            Whitelist::create(true, true,'Acme\*'),
            'acme\foo',
            true,
        ];

        yield [
            Whitelist::create(true, true,'Acme\Foo\*'),
            'Acme\Foo\Bar',
            true,
        ];

        yield [
            Whitelist::create(true, true,'Acme\Foo\*'),
            'acme\foo\bar',
            true,
        ];

        yield [
            Whitelist::create(true, true,'\*'),
            'Acme',
            true,
        ];

        yield [
            Whitelist::create(true, true,'\*'),
            'acme',
            true,
        ];

        yield [
            Whitelist::create(true, true,'\*'),
            'Acme\Foo',
            true,
        ];

        yield [
            Whitelist::create(true, true,'\*'),
            'acme\foo',
            true,
        ];
    }

    public function provideWhitelistToConvert()
    {
        yield [
            Whitelist::create(true, true),
            [],
        ];

        yield [
            Whitelist::create(true, true,'Acme\Foo'),
            ['Acme\Foo'],
        ];

        yield [
            Whitelist::create(true, true,'\Acme\Foo'),
            ['Acme\Foo'],
        ];

        yield [
            Whitelist::create(true, true,'Acme\Foo\*'),
            ['Acme\Foo\*'],
        ];

        yield [
            Whitelist::create(true, true,'\Acme\Foo\*'),
            ['Acme\Foo\*'],
        ];

        yield [
            Whitelist::create(true, true,'*'),
            ['*'],
        ];

        yield [
            Whitelist::create(true, true,'\*'),
            ['*'],
        ];

        yield [
            Whitelist::create(true, true,'Acme', 'Acme\Foo', 'Acme\Foo\*', '*'),
            ['Acme', 'Acme\Foo', 'Acme\Foo\*', '*'],
        ];

        yield [
            Whitelist::create(true, true,'Acme', 'Acme'),
            ['Acme'],
        ];
    }
}
