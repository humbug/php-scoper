<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Configuration;

use Humbug\PhpScoper\RegexChecker;
use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Humbug\PhpScoper\Configuration\ConfigurationSymbolsConfigurationFactory
 */
final class ConfigurationSymbolsConfigurationFactoryTest extends TestCase
{
    private ConfigurationSymbolsConfigurationFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ConfigurationSymbolsConfigurationFactory(
            new RegexChecker(),
        );
    }

    /**
     * @dataProvider configProvider
     */
    public function test_it_can_create_a_symbols_config_object_from_the_config(
        array $config,
        SymbolsConfiguration $expected
    ): void
    {
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
            SymbolsConfiguration::create(
                true,
            ),
        ];

        // TODO: named parameters would be handy here
        yield 'expose global classes' => [
            [
                ConfigurationKeys::EXPOSE_GLOBAL_CLASSES_KEYWORD => true,
            ],
            SymbolsConfiguration::create(
                false,
                true,
            ),
        ];

        yield 'expose global functions' => [
            [
                ConfigurationKeys::EXPOSE_GLOBAL_FUNCTIONS_KEYWORD => true,
            ],
            SymbolsConfiguration::create(
                false,
                false,
                true,
            ),
        ];

        yield 'exclude exact namespace' => [
            [
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => [
                    'PHPUnit\Runner',
                ],
            ],
            SymbolsConfiguration::create(
                false,
                false,
                false,
                NamespaceRegistry::create(
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
                false,
                false,
                false,
                NamespaceRegistry::create(
                    [],
                    ['~^PHPUnit\\Runner(\\.*)?$~i'],
                ),
            ),
        ];

        yield 'expose element' => [
            [
                ConfigurationKeys::WHITELIST_KEYWORD => [
                    'Acme\Foo',
                ],
            ],
            SymbolsConfiguration::create(
                false,
                false,
                false,
                null,
                null,
                ['acme\foo'],
                [],
                ['acme\foo'],
                [],
                ['acme\Foo'],
                [],
            ),
        ];

        yield 'legacy expose namespace' => [
            [
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => [
                    'PHPUnit\Internal',
                ],
                ConfigurationKeys::WHITELIST_KEYWORD => [
                    'PHPUnit\Runner\*',
                ],
            ],
            SymbolsConfiguration::create(
                false,
                false,
                false,
                NamespaceRegistry::create(
                    [
                        'PHPUnit\Internal',
                        'PHPUnit\Runner',
                    ],
                ),
            ),
        ];

        yield 'nominal' => [
            [
                ConfigurationKeys::EXPOSE_GLOBAL_CONSTANTS_KEYWORD => true,
                ConfigurationKeys::EXPOSE_GLOBAL_CLASSES_KEYWORD => true,
                ConfigurationKeys::EXPOSE_GLOBAL_FUNCTIONS_KEYWORD => true,
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => [
                    'PHPUnit\Internal',
                    '~^PHPUnit\\Runner(\\.*)?$~',
                ],
                ConfigurationKeys::WHITELIST_KEYWORD => [
                    'PHPUnit\Runner\*',
                    'Acme\Foo',
                ],
            ],
            SymbolsConfiguration::create(
                true,
                true,
                true,
                NamespaceRegistry::create(
                    [
                        'PHPUnit\Internal',
                        'PHPUnit\Runner',
                    ],
                    [
                        '~^PHPUnit\\Runner(\\.*)?$~i',
                    ],
                ),
                null,
                ['acme\foo'],
                [],
                ['acme\foo'],
                [],
                ['acme\Foo'],
                [],
            ),
        ];
    }

    /**
     * @dataProvider invalidConfigProvider
     */
    public function test_it_cannot_create_a_whitelist_from_an_invalid_config(
        array $config,
        string $expectedExceptionClassName,
        string $expectedExceptionMessage
    ): void
    {
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
            'Expected "exclude-namespaces" to be an array of strings, got "string" instead.',
        ];

        yield 'exclude namespace is not an array of strings' => [
            [
                ConfigurationKeys::EXCLUDE_NAMESPACES_KEYWORD => [false],
            ],
            InvalidArgumentException::class,
            'Expected "exclude-namespaces" to be an array of strings, got "boolean" for the element with the index "0".',
        ];

        // TODO: need to find a case
        //yield 'exclude namespace contains an invalid regex-like expression' => [];

        yield 'whitelist is not an array' => [
            [
                ConfigurationKeys::WHITELIST_KEYWORD => true,
            ],
            InvalidArgumentException::class,
            'Expected "whitelist" to be an array of strings, found "boolean" instead.',
        ];

        yield 'whitelist is not an array of strings' => [
            [
                ConfigurationKeys::WHITELIST_KEYWORD => [true],
            ],
            InvalidArgumentException::class,
            'Expected whitelist to be an array of string, the "0" element is not.',
        ];
    }
}
