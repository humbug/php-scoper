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

use Generator;
use PhpParser\Node\Name\FullyQualified;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use function array_flip;

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
        array $expectedSymbols,
        array $expectedConstants
    ) {
        $whitelistObject = Whitelist::create(true, true, ...$whitelist);

        $whitelistReflection = new ReflectionClass(Whitelist::class);

        $whitelistSymbolReflection = $whitelistReflection->getProperty('symbols');
        $whitelistSymbolReflection->setAccessible(true);
        $actualSymbols = $whitelistSymbolReflection->getValue($whitelistObject);

        $whitelistNamespaceReflection = $whitelistReflection->getProperty('namespaces');
        $whitelistNamespaceReflection->setAccessible(true);
        $actualNamespaces = $whitelistNamespaceReflection->getValue($whitelistObject);

        $whitelistConstantReflection = $whitelistReflection->getProperty('constants');
        $whitelistConstantReflection->setAccessible(true);
        $actualConstants = $whitelistConstantReflection->getValue($whitelistObject);

        $this->assertTrue($whitelistObject->whitelistGlobalConstants());
        $this->assertTrue($whitelistObject->whitelistGlobalFunctions());
        $this->assertSame($expectedNamespaces, $actualNamespaces);
        $this->assertSame($expectedSymbols, array_flip($actualSymbols));
        $this->assertSame($expectedConstants, array_flip($actualConstants));

        $whitelistObject = Whitelist::create(false, false, ...$whitelist);

        $this->assertFalse($whitelistObject->whitelistGlobalConstants());
        $this->assertFalse($whitelistObject->whitelistGlobalFunctions());
        $this->assertSame($expectedNamespaces, $actualNamespaces);
        $this->assertSame($expectedSymbols, array_flip($actualSymbols));
        $this->assertSame($expectedConstants, array_flip($actualConstants));
    }

    /**
     * @dataProvider provideGlobalConstantNames
     */
    public function test_it_can_tell_if_a_constant_is_a_whitelisted_global_constant(Whitelist $whitelist, string $constant, bool $expected)
    {
        $actual = $whitelist->isGlobalWhitelistedConstant($constant);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideGlobalClassNames
     */
    public function test_it_can_tell_if_a_class_is_a_whitelisted_global_class(Whitelist $whitelist, string $constant, bool $expected)
    {
        $this->markTestSkipped('TODO');
        $actual = $whitelist->isGlobalWhitelistedClass($constant);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideGlobalFunctionNames
     */
    public function test_it_can_tell_if_a_function_is_a_whitelisted_global_function(Whitelist $whitelist, string $constant, bool $expected)
    {
        $actual = $whitelist->isGlobalWhitelistedFunction($constant);

        $this->assertSame($expected, $actual);
    }

    public function test_it_can_record_whitelisted_functions()
    {
        $whitelist = Whitelist::create(true, true);

        $whitelist->recordWhitelistedFunction(
            new FullyQualified('Acme\foo'),
            new FullyQualified('Humbug\Acme\foo')
        );
        $whitelist->recordWhitelistedFunction(
            new FullyQualified('Acme\foo'),
            new FullyQualified('Humbug\Acme\foo')
        );
        $whitelist->recordWhitelistedFunction(
            new FullyQualified('Acme\bar'),
            new FullyQualified('Humbug\Acme\bar')
        );

        $this->assertSame(
            [
                ['Acme\foo', 'Humbug\Acme\foo'],
                ['Acme\bar', 'Humbug\Acme\bar'],
            ],
            $whitelist->getWhitelistedFunctions()
        );
    }

    public function test_it_can_record_whitelisted_classes()
    {
        $this->markTestSkipped('TODO');
        $whitelist = Whitelist::create(true, true);

        $whitelist->recordWhitelistedClass(
            new FullyQualified('Acme\foo'),
            new FullyQualified('Humbug\Acme\foo')
        );
        $whitelist->recordWhitelistedClass(
            new FullyQualified('Acme\foo'),
            new FullyQualified('Humbug\Acme\foo')
        );
        $whitelist->recordWhitelistedClass(
            new FullyQualified('Acme\bar'),
            new FullyQualified('Humbug\Acme\bar')
        );

        $this->assertSame(
            [
                ['Acme\foo', 'Humbug\Acme\foo'],
                ['Acme\bar', 'Humbug\Acme\bar'],
            ],
            $whitelist->getWhitelistedFunctions()
        );
    }

    /**
     * @dataProvider provideSymbolNames
     */
    public function test_it_can_tell_if_a_symbol_is_whitelisted(Whitelist $whitelist, string $symbol, bool $caseSensitive, bool $expected)
    {
        $actual = $whitelist->isSymbolWhitelisted($symbol, $caseSensitive);

        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider provideNamespaceWhitelists
     */
    public function test_it_can_tell_if_a_namespace_is_whitelisted(Whitelist $whitelist, string $class, bool $expected)
    {
        $actual = $whitelist->belongsToWhitelistedNamespace($class);

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

    public function provideWhitelists(): Generator
    {
        yield [[], [], [], []];

        yield [['Acme\Foo'], [], ['acme\foo'], ['acme\Foo']];

        yield [['Acme\Foo\*'], ['acme\foo'], [], []];

        yield [['\*'], [''], [], []];

        yield [['*'], [''], [], []];

        yield [
            ['Acme\Foo', 'Acme\Foo\*', '\*'],
            ['acme\foo', ''],
            ['acme\foo'],
            ['acme\Foo'],
        ];
    }

    public function provideGlobalConstantNames(): Generator
    {
        yield [
            Whitelist::create(true, true),
            'PHP_SCOPER_VERSION',
            true,
        ];

        yield [
            Whitelist::create(false, true),
            'PHP_SCOPER_VERSION',
            false,
        ];

        yield [
            Whitelist::create(true, true),
            'Humbug\PHP_SCOPER_VERSION',
            false,
        ];

        yield [
            Whitelist::create(false, true),
            'Humbug\PHP_SCOPER_VERSION',
            false,
        ];

        yield [
            Whitelist::create(true, true),
            'Humbug\PHP_SCOPER_VERSION',
            false,
        ];

        yield [
            Whitelist::create(false, true, 'PHP_SCOPER_VERSION'),
            'PHP_SCOPER_VERSION',
            false,
        ];
    }

    public function provideGlobalClassNames(): Generator
    {
        yield [
            Whitelist::create(true, true, 'PHP_SCOPER_VERSION'),
            'Foo',
            false,
        ];
    }

    public function provideGlobalFunctionNames(): Generator
    {
        yield [
            Whitelist::create(true, true, 'PHP_SCOPER_VERSION'),
            'foo',
            true,
        ];

        yield [
            Whitelist::create(true, false, 'PHP_SCOPER_VERSION'),
            'foo',
            false,
        ];

        yield [
            Whitelist::create(true, true, 'PHP_SCOPER_VERSION'),
            'Acme\foo',
            false,
        ];

        yield [
            Whitelist::create(true, false, 'PHP_SCOPER_VERSION'),
            'Acme\foo',
            false,
        ];
    }

    public function provideSymbolNames(): Generator
    {
        yield [
            Whitelist::create(true, true),
            'Acme\Foo',
            false,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\Foo'),
            'Acme\Foo',
            false,
            true,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\Foo'),
            'acme\foo',
            false,
            true,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\Foo'),
            'acme\foo',
            true,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\Foo'),
            'Acme\Foo\Bar',
            true,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\Foo'),
            'Acme',
            true,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme'),
            'Acme',
            true,
            true,
        ];

        yield [
            Whitelist::create(true, true, 'Acme'),
            'Acme',
            false,
            true,
        ];

        yield [
            Whitelist::create(true, true, 'Acme'),
            'acme',
            false,
            true,
        ];

        yield [
            Whitelist::create(true, true, 'Acme'),
            'acme',
            true,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\*'),
            'Acme',
            true,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\*'),
            'Acme',
            false,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\*'),
            'acme',
            true,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\*'),
            'acme',
            false,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\*'),
            'Acme\Foo',
            true,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\*'),
            'Acme\Foo',
            false,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\*'),
            'acme\Foo',
            true,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\*'),
            'acme\Foo',
            false,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\F*'),
            'Acme',
            true,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\F*'),
            'Acme',
            false,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\F*'),
            'acme',
            true,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\F*'),
            'acme',
            false,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\F*'),
            'Acme\Foo',
            true,
            true,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\F*'),
            'Acme\Foo',
            false,
            true,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\F*'),
            'acme\foo',
            true,
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\F*'),
            'acme\foo',
            false,
            true,
        ];
    }

    public function provideNamespaceWhitelists(): Generator
    {
        yield [
            Whitelist::create(true, true),
            'Acme\Foo',
            false,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\Foo\*'),
            'Acme\Foo',
            true,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\Foo\*'),
            'acme\foo',
            true,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\*'),
            'Acme\Foo',
            true,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\*'),
            'acme\foo',
            true,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\Foo\*'),
            'Acme\Foo\Bar',
            true,
        ];

        yield [
            Whitelist::create(true, true, 'Acme\Foo\*'),
            'acme\foo\bar',
            true,
        ];

        yield [
            Whitelist::create(true, true, '\*'),
            'Acme',
            true,
        ];

        yield [
            Whitelist::create(true, true, '\*'),
            'acme',
            true,
        ];

        yield [
            Whitelist::create(true, true, '\*'),
            'Acme\Foo',
            true,
        ];

        yield [
            Whitelist::create(true, true, '\*'),
            'acme\foo',
            true,
        ];
    }

    public function provideWhitelistToConvert(): Generator
    {
        yield [
            Whitelist::create(true, true),
            [],
        ];

        yield [
            Whitelist::create(true, true, 'Acme\Foo'),
            ['Acme\Foo'],
        ];

        yield [
            Whitelist::create(true, true, '\Acme\Foo'),
            ['Acme\Foo'],
        ];

        yield [
            Whitelist::create(true, true, 'Acme\Foo\*'),
            ['Acme\Foo\*'],
        ];

        yield [
            Whitelist::create(true, true, '\Acme\Foo\*'),
            ['Acme\Foo\*'],
        ];

        yield [
            Whitelist::create(true, true, '*'),
            ['*'],
        ];

        yield [
            Whitelist::create(true, true, '\*'),
            ['*'],
        ];

        yield [
            Whitelist::create(true, true, 'Acme', 'Acme\Foo', 'Acme\Foo\*', '*'),
            ['Acme', 'Acme\Foo', 'Acme\Foo\*', '*'],
        ];

        yield [
            Whitelist::create(true, true, 'Acme', 'Acme'),
            ['Acme'],
        ];
    }
}
