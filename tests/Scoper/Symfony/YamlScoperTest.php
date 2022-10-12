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

namespace Humbug\PhpScoper\Scoper\Symfony;

use Humbug\PhpScoper\Configuration\SymbolsConfiguration;
use Humbug\PhpScoper\Scoper\Scoper;
use Humbug\PhpScoper\Symbol\EnrichedReflector;
use Humbug\PhpScoper\Symbol\NamespaceRegistry;
use Humbug\PhpScoper\Symbol\Reflector;
use Humbug\PhpScoper\Symbol\SymbolRegistry;
use Humbug\PhpScoper\Symbol\SymbolsRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use function is_a;

/**
 * @covers \Humbug\PhpScoper\Scoper\Symfony\YamlScoper
 *
 * @internal
 */
class YamlScoperTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<Scoper>
     */
    private ObjectProphecy $decoratedScoperProphecy;

    private Scoper $decoratedScoper;

    protected function setUp(): void
    {
        $this->decoratedScoperProphecy = $this->prophesize(Scoper::class);
        $this->decoratedScoper = $this->decoratedScoperProphecy->reveal();
    }

    public function test_it_is_a_scoper(): void
    {
        self::assertTrue(is_a(YamlScoper::class, Scoper::class, true));
    }

    /**
     * @dataProvider provideYamlFilesExtensions
     */
    public function test_it_can_scope_yaml_files(string $file, bool $scoped): void
    {
        $prefix = 'Humbug';

        $contents = '';

        if (false === $scoped) {
            $this->decoratedScoperProphecy
                ->scope(Argument::cetera())
                ->willReturn($expected = 'scoped by decorated scoper');
            $scopedCount = 1;
        } else {
            $expected = $contents;
            $scopedCount = 0;
        }

        $scoper = new YamlScoper(
            $this->decoratedScoper,
            $prefix,
            new EnrichedReflector(
                Reflector::createEmpty(),
                SymbolsConfiguration::create(),
            ),
            new SymbolsRegistry(),
        );

        $actual = $scoper->scope($file, $contents);

        self::assertSame($expected, $actual);

        $this->decoratedScoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes($scopedCount);
    }

    /**
     * @dataProvider provideYamlFiles
     */
    public function test_it_scopes_yaml_files(
        string $contents,
        SymbolsConfiguration $symbolsConfiguration,
        string $expected,
        array $expectedClasses
    ): void {
        $prefix = 'Humbug';
        $file = 'file.yaml';

        $symbolsRegistry = new SymbolsRegistry();

        $scoper = new YamlScoper(
            $this->decoratedScoper,
            $prefix,
            new EnrichedReflector(
                Reflector::createEmpty(),
                $symbolsConfiguration,
            ),
            $symbolsRegistry,
        );

        $actual = $scoper->scope($file, $contents);

        self::assertSame($expected, $actual);

        self::assertSame($expectedClasses, $symbolsRegistry->getRecordedClasses());
        self::assertSame([], $symbolsRegistry->getRecordedFunctions());

        $this->decoratedScoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes(0);
    }

    public static function provideYamlFilesExtensions(): iterable
    {
        yield ['file.yaml', true];
        yield ['file.yml', true];
        yield ['file.YAML', true];
        yield ['file.YML', true];
        yield ['file.yam', false];
        yield ['file.aml', false];
        yield ['file', false];
    }

    public static function provideYamlFiles(): iterable
    {
        yield 'empty' => [
            '',
            SymbolsConfiguration::create(),
            '',
            [],
        ];

        yield 'not quoted service definitions' => [
            <<<'YAML'
                services:
                    Symfony\Component\Console\Style\SymfonyStyle: ~
                    Symfony\Component\Console\Input\InputInterface:
                        alias: 'Symfony\Component\Console\Input\ArgvInput'
                    Symfony\Component\Console\Output\OutputInterface: '@Symfony\Component\Console\Output\ConsoleOutput'
                YAML,
            SymbolsConfiguration::create(),
            <<<'YAML'
                services:
                    Humbug\Symfony\Component\Console\Style\SymfonyStyle: ~
                    Humbug\Symfony\Component\Console\Input\InputInterface:
                        alias: 'Humbug\Symfony\Component\Console\Input\ArgvInput'
                    Humbug\Symfony\Component\Console\Output\OutputInterface: '@Humbug\Symfony\Component\Console\Output\ConsoleOutput'
                YAML,
            [],
        ];

        yield 'not quoted service definitions with whitelist' => [
            <<<'YAML'
                services:
                    Symfony\Component\Console\Style\SymfonyStyle: ~
                    Symfony\Component\Console\Input\InputInterface:
                        alias: 'Symfony\Component\Console\Input\ArgvInput'
                    Symfony\Component\Finder\Output\OutputInterface: '@Symfony\Component\Console\Output\ConsoleOutput'
                YAML,
            SymbolsConfiguration::create(
                true,
                true,
                true,
                NamespaceRegistry::create(
                    ['Symfony\Component\Console'],
                ),
            ),
            <<<'YAML'
                services:
                    Symfony\Component\Console\Style\SymfonyStyle: ~
                    Symfony\Component\Console\Input\InputInterface:
                        alias: 'Symfony\Component\Console\Input\ArgvInput'
                    Humbug\Symfony\Component\Finder\Output\OutputInterface: '@Symfony\Component\Console\Output\ConsoleOutput'
                YAML,
            [],
        ];

        yield 'quoted service definitions' => [
            <<<'YAML'
                services:
                    "Symfony\\Component\\Console\\Style\\SymfonyStyle": ~
                    "Symfony\\Component\\Console\\Input\\InputInterface":
                        alias: "Symfony\\Component\\Console\\Input\\ArgvInput"
                    "Symfony\\Component\\Console\\Output\\OutputInterface": "@Symfony\\Component\\Console\\Output\\ConsoleOutput"
                YAML,
            SymbolsConfiguration::create(),
            <<<'YAML'
                services:
                    "Humbug\\Symfony\\Component\\Console\\Style\\SymfonyStyle": ~
                    "Humbug\\Symfony\\Component\\Console\\Input\\InputInterface":
                        alias: "Humbug\\Symfony\\Component\\Console\\Input\\ArgvInput"
                    "Humbug\\Symfony\\Component\\Console\\Output\\OutputInterface": "@Humbug\\Symfony\\Component\\Console\\Output\\ConsoleOutput"
                YAML,
            [],
        ];

        yield 'quoted service definitions with whitelist' => [
            <<<'YAML'
                services:
                    "Symfony\\Component\\Console\\Style\\SymfonyStyle": ~
                    "Symfony\\Component\\Console\\Input\\InputInterface": '@Symfony\Component\Console\Style\SymfonyStyle'
                YAML,
            SymbolsConfiguration::create(),
            <<<'YAML'
                services:
                    "Humbug\\Symfony\\Component\\Console\\Style\\SymfonyStyle": ~
                    "Humbug\\Symfony\\Component\\Console\\Input\\InputInterface": '@Humbug\Symfony\Component\Console\Style\SymfonyStyle'
                YAML,
            [],
        ];

        yield 'PSR-4 service locator' => [
            <<<'YAML'
                services:
                    Acme\Controller\:
                        resource: "../src"

                    Bar\Controller\:
                        resource: "../src"
                YAML,
            SymbolsConfiguration::create(),
            <<<'YAML'
                services:
                    Humbug\Acme\Controller\:
                        resource: "../src"

                    Humbug\Bar\Controller\:
                        resource: "../src"
                YAML,
            [],
        ];

        yield 'PSR-4 service locator with whitelist' => [
            <<<'YAML'
                services:
                    Acme\Controller\:
                        resource: "../src"

                    Bar\Controller\:
                        resource: "../src"
                YAML,
            SymbolsConfiguration::create(
                true,
                true,
                true,
                NamespaceRegistry::create(
                    ['Acme\Controller'],
                ),
            ),
            <<<'YAML'
                services:
                    Acme\Controller\:
                        resource: "../src"

                    Humbug\Bar\Controller\:
                        resource: "../src"
                YAML,
            [],
        ];

        yield 'service as alias' => [
            <<<'YAML'
                services:
                    Acme\Foo: '@Acme\Foo\Bar'
                    Acme\Foo: '@Acme\Bar\Acme\Foo'
                YAML,
            SymbolsConfiguration::create(),
            <<<'YAML'
                services:
                    Humbug\Acme\Foo: '@Humbug\Acme\Foo\Bar'
                    Humbug\Acme\Foo: '@Humbug\Acme\Bar\Acme\Foo'
                YAML,
            [],
        ];

        yield 'service as alias with whitelist' => [
            <<<'YAML'
                services:
                    Acme\Foo\X: '@Acme\Foo\Bar'
                    Acme\Bar: '@Acme\Bar\Acme\Foo'
                YAML,
            SymbolsConfiguration::create(
                true,
                true,
                true,
                NamespaceRegistry::create(
                    ['Acme\Foo'],
                ),
            ),
            <<<'YAML'
                services:
                    Acme\Foo\X: '@Acme\Foo\Bar'
                    Humbug\Acme\Bar: '@Humbug\Acme\Bar\Acme\Foo'
                YAML,
            [],
        ];

        yield 'service with class-name as argument with short-argument notation' => [
            <<<'YAML'
                services:
                    Acme\Foo:
                        - '@Acme\Bar'
                YAML,
            SymbolsConfiguration::create(),
            <<<'YAML'
                services:
                    Humbug\Acme\Foo:
                        - '@Humbug\Acme\Bar'
                YAML,
            [],
        ];

        yield 'service with class-name as argument with short-argument notation with whitelist' => [
            <<<'YAML'
                services:
                    Acme\Foo\X:
                        - '@Acme\Foo\Y'

                    Acme\Bar\X:
                        - '@Acme\Bar\Y'
                YAML,
            SymbolsConfiguration::create(
                true,
                true,
                true,
                NamespaceRegistry::create(
                    ['Acme\Foo'],
                ),
            ),
            <<<'YAML'
                services:
                    Acme\Foo\X:
                        - '@Acme\Foo\Y'

                    Humbug\Acme\Bar\X:
                        - '@Humbug\Acme\Bar\Y'
                YAML,
            [],
        ];

        yield 'service with class alias key, class as argument and class in tag attribute' => [
            <<<'YAML'
                services:
                    foo:
                        class: 'Acme\Foo'
                        arguments:
                            - '@Acme\Bar'
                        tags:
                            - { name: my_tag, id: 'Acme\Baz' }
                YAML,
            SymbolsConfiguration::create(),
            <<<'YAML'
                services:
                    foo:
                        class: 'Humbug\Acme\Foo'
                        arguments:
                            - '@Humbug\Acme\Bar'
                        tags:
                            - { name: my_tag, id: 'Humbug\Acme\Baz' }
                YAML,
            [],
        ];

        yield 'service with class alias key, class as argument and class in tag attribute with whitelist' => [
            <<<'YAML'
                services:
                    foo:
                        class: 'Acme\Foo\X'
                        arguments:
                            - '@Acme\Foo\Y'
                        tags:
                            - { name: my_tag, id: 'Acme\Foo\Z' }

                    bar:
                        class: 'Acme\Bar\X'
                        arguments:
                            - '@Acme\Bar\Y'
                        tags:
                            - { name: my_tag, id: 'Acme\Bar\Z' }
                YAML,
            SymbolsConfiguration::create(
                true,
                true,
                true,
                NamespaceRegistry::create(
                    ['Acme\Foo'],
                ),
            ),
            <<<'YAML'
                services:
                    foo:
                        class: 'Acme\Foo\X'
                        arguments:
                            - '@Acme\Foo\Y'
                        tags:
                            - { name: my_tag, id: 'Acme\Foo\Z' }

                    bar:
                        class: 'Humbug\Acme\Bar\X'
                        arguments:
                            - '@Humbug\Acme\Bar\Y'
                        tags:
                            - { name: my_tag, id: 'Humbug\Acme\Bar\Z' }
                YAML,
            [],
        ];

        yield [
            <<<'YAML'
                services:
                    Acme\Foo:
                        - '@Acme\Bar'
                YAML,
            SymbolsConfiguration::create(
                true,
                true,
                true,
                null,
                null,
                SymbolRegistry::create(['Acme\Foo']),
            ),
            <<<'YAML'
                services:
                    Humbug\Acme\Foo:
                        - '@Humbug\Acme\Bar'
                YAML,
            [
                ['Acme\Foo', 'Humbug\Acme\Foo'],
            ],
        ];

        yield [
            <<<'YAML'
                services:
                    Foo:
                        - '@Acme\Bar'

                    Closure: ~
                YAML,
            SymbolsConfiguration::create(),
            <<<'YAML'
                services:
                    Foo:
                        - '@Humbug\Acme\Bar'

                    Closure: ~
                YAML,
            [], // Whitelisting global classes in the service definitions is not supported at the moment. Provide a PR
            // if you are willing to add support for it.
        ];

        yield [
            <<<'YAML'
                services:
                    Acme\Foo:
                        - '@Acme\Bar'
                    Emca\Foo:
                        - '@Emca\Bar'
                YAML,
            SymbolsConfiguration::create(
                true,
                true,
                true,
                NamespaceRegistry::create(
                    ['Acme'],
                ),
            ),
            <<<'YAML'
                services:
                    Acme\Foo:
                        - '@Acme\Bar'
                    Humbug\Emca\Foo:
                        - '@Humbug\Emca\Bar'
                YAML,
            [],
        ];

        yield 'symfony skeleton example' => [
            <<<'YAML'
                # This file is the entry point to configure your own services.
                # Files in the packages/ subdirectory configure your dependencies.

                # Put parameters here that don't need to change on each machine where the app is deployed
                # https://symfony.com/doc/current/best_practices/configuration.html#application-related-configuration
                parameters:

                services:
                    # default configuration for services in *this* file
                    _defaults:
                        autowire: true      # Automatically injects dependencies in your services.
                        autoconfigure: true # Automatically registers your services as commands, event subscribers, etc.
                        public: false       # Allows optimizing the container by removing unused services; this also means
                                            # fetching services directly from the container via $container->get() won't work.
                                            # The best practice is to be explicit about your dependencies anyway.

                    # makes classes in src/ available to be used as services
                    # this creates a service per class whose id is the fully-qualified class name
                    App\:
                        resource: '../src/*'
                        exclude: '../src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}'

                    # controllers are imported separately to make sure services can be injected
                    # as action arguments even if you don't extend any base controller class
                    App\Controller\:
                        resource: '../src/Controller'
                        tags: ['controller.service_arguments']

                    # add more service definitions when explicit configuration is needed
                    # please note that last definitions always *replace* previous ones

                YAML,
            SymbolsConfiguration::create(),
            <<<'YAML'
                # This file is the entry point to configure your own services.
                # Files in the packages/ subdirectory configure your dependencies.

                # Put parameters here that don't need to change on each machine where the app is deployed
                # https://symfony.com/doc/current/best_practices/configuration.html#application-related-configuration
                parameters:

                services:
                    # default configuration for services in *this* file
                    _defaults:
                        autowire: true      # Automatically injects dependencies in your services.
                        autoconfigure: true # Automatically registers your services as commands, event subscribers, etc.
                        public: false       # Allows optimizing the container by removing unused services; this also means
                                            # fetching services directly from the container via $container->get() won't work.
                                            # The best practice is to be explicit about your dependencies anyway.

                    # makes classes in src/ available to be used as services
                    # this creates a service per class whose id is the fully-qualified class name
                    Humbug\App\:
                        resource: '../src/*'
                        exclude: '../src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}'

                    # controllers are imported separately to make sure services can be injected
                    # as action arguments even if you don't extend any base controller class
                    Humbug\App\Controller\:
                        resource: '../src/Controller'
                        tags: ['controller.service_arguments']

                    # add more service definitions when explicit configuration is needed
                    # please note that last definitions always *replace* previous ones

                YAML,
            [],
        ];
    }
}
