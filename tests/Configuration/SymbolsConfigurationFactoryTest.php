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

namespace Humbug\PhpScoper\Configuration;

use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use Humbug\PhpScoper\Symbol\SymbolRegistry;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @covers \Humbug\PhpScoper\Configuration\SymbolsConfigurationFactory
 *
 * @internal
 */
final class SymbolsConfigurationFactoryTest extends TestCase
{
    private SymbolsConfigurationFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new SymbolsConfigurationFactory(
            new RegexChecker(),
        );
    }

    /**
     * @dataProvider configProvider
     */
    public function test_it_can_create_a_symbols_config_object_from_the_config(
        array $config,
        SymbolsConfiguration $expected
    ): void {
        $actual = $this->factory->createSymbolsConfiguration($config);

        self::assertEquals($expected, $actual);
    }

    public static function configProvider(): iterable
    {
        yield 'empty config' => [
            [],
            SymbolsConfiguration::create(),
        ];

        yield 'expose global constants' => [
            [
                ConfigurationKeys::EXPOSE_GLOBAL_CONSTANTS_KEYWORD => true,
            ],
            SymbolsConfiguration::create(),
        ];

        yield 'do not expose global constants' => [
            [
                ConfigurationKeys::EXPOSE_GLOBAL_CONSTANTS_KEYWORD => false,
            ],
            SymbolsConfiguration::create(
                false,
            ),
        ];

        yield 'do not expose global classes' => [
            [
                ConfigurationKeys::EXPOSE_GLOBAL_CLASSES_KEYWORD => false,
            ],
            SymbolsConfiguration::create(
                exposeGlobalClasses: false,
            ),
        ];

        yield 'do not expose global functions' => [
            [
                ConfigurationKeys::EXPOSE_GLOBAL_FUNCTIONS_KEYWORD => false,
            ],
            SymbolsConfiguration::create(
                exposeGlobalFunctions: false,
            ),
        ];

        yield 'exclude exact namespace' => [
            [
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => [
                    'PHPUnit\Runner',
                ],
            ],
            SymbolsConfiguration::create(
                excludedNamespaces: NamespaceRegistry::create(
                    ['PHPUnit\Runner'],
                ),
            ),
        ];

        yield 'exclude namespace regex' => [
            [
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => [
                    '~^PHPUnit\\Runner(\\.*)?$~',
                ],
            ],
            SymbolsConfiguration::create(
                excludedNamespaces: NamespaceRegistry::create(
                    [],
                    ['~^PHPUnit\\Runner(\\.*)?$~i'],
                ),
            ),
        ];

        yield 'exclude namespace regex with flags' => [
            [
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => [
                    '~^PHPUnit\\Runner(\\.*)?$~u',
                ],
            ],
            SymbolsConfiguration::create(
                excludedNamespaces: NamespaceRegistry::create(
                    [],
                    ['~^PHPUnit\\Runner(\\.*)?$~ui'],
                ),
            ),
        ];

        yield 'exclude namespace regex with case insensitive flag' => [
            [
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => [
                    '~^PHPUnit\\Runner(\\.*)?$~i',
                ],
            ],
            SymbolsConfiguration::create(
                excludedNamespaces: NamespaceRegistry::create(
                    [],
                    ['~^PHPUnit\\Runner(\\.*)?$~i'],
                ),
            ),
        ];

        yield 'exclude namespace regex with several flags flag' => [
            [
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => [
                    '~^PHPUnit\\Runner(\\.*)?$~uiA',
                ],
            ],
            SymbolsConfiguration::create(
                excludedNamespaces: NamespaceRegistry::create(
                    [],
                    ['~^PHPUnit\\Runner(\\.*)?$~uiA'],
                ),
            ),
        ];

        yield 'nominal' => [
            [
                ConfigurationKeys::EXPOSE_GLOBAL_CONSTANTS_KEYWORD => false,
                ConfigurationKeys::EXPOSE_GLOBAL_CLASSES_KEYWORD => false,
                ConfigurationKeys::EXPOSE_GLOBAL_FUNCTIONS_KEYWORD => false,
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => [
                    'PHPUnit\Internal',
                    '~^PHPUnit\\Runner(\\.*)?$~',
                ],
            ],
            SymbolsConfiguration::create(
                false,
                false,
                false,
                NamespaceRegistry::create(
                    [
                        'PHPUnit\Internal',
                    ],
                    [
                        '~^PHPUnit\\Runner(\\.*)?$~i',
                    ],
                ),
                null,
                SymbolRegistry::create(),
                SymbolRegistry::create(),
                SymbolRegistry::createForConstants(),
            ),
        ];
    }

    /**
     * @dataProvider invalidConfigProvider
     *
     * @param class-string<Throwable> $expectedExceptionClassName
     */
    public function test_it_cannot_create_a_symbols_config_from_an_invalid_config(
        array $config,
        string $expectedExceptionClassName,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedExceptionClassName);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->factory->createSymbolsConfiguration($config);
    }

    public static function invalidConfigProvider(): iterable
    {
        yield 'expose global is not a bool' => [
            [
                ConfigurationKeys::EXPOSE_GLOBAL_FUNCTIONS_KEYWORD => '',
            ],
            InvalidArgumentException::class,
            'Expected expose-global-functions to be a boolean, found "string" instead.',
        ];

        yield 'exclude namespace is not an array' => [
            [
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => '',
            ],
            InvalidArgumentException::class,
            'Expected "exclude-namespaces" to be an array of strings, found "string" instead.',
        ];

        yield 'exclude namespace is not an array of strings' => [
            [
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => [false],
            ],
            InvalidArgumentException::class,
            'Expected "exclude-namespaces" to be an array of strings, found "bool" for the element with the index "0".',
        ];

        yield 'exclude namespace is not an array of strings (string index)' => [
            [
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => ['foo' => false],
            ],
            InvalidArgumentException::class,
            'Expected "exclude-namespaces" to be an array of strings, found "bool" for the element with the index "foo".',
        ];

        // TODO: need to find a case
        // yield 'exclude namespace contains an invalid regex-like expression' => [];
    }
}
