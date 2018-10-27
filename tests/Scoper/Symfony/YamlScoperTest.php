<?php

declare(strict_types=1);

namespace Humbug\PhpScoper\Scoper\Symfony;

use Generator;
use function Humbug\PhpScoper\create_fake_patcher;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Scoper\Symfony\YamlScoper;
use Humbug\PhpScoper\Whitelist;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \Humbug\PhpScoper\Scoper\Symfony\YamlScoper
 */
class YamlScoperTest extends TestCase
{
    /**
     * @var Scoper
     */
    private $scoper;

    /**
     * @var Scoper|ObjectProphecy
     */
    private $decoratedScoperProphecy;

    /**
     * @var Scoper
     */
    private $decoratedScoper;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        $this->decoratedScoperProphecy = $this->prophesize(Scoper::class);
        $this->decoratedScoper = $this->decoratedScoperProphecy->reveal();

        $this->scoper = new YamlScoper($this->decoratedScoper);
    }

    public function test_it_is_a_Scoper(): void
    {
        $this->assertTrue(is_a(YamlScoper::class, Scoper::class, true));
    }

    /**
     * @dataProvider provideYamlFilesExtensions
     */
    public function test_it_can_scope_Yaml_files(string $file, bool $scoped): void
    {
        $prefix = 'Humbug';
        $patchers = [create_fake_patcher()];
        $whitelist = Whitelist::create(true, true, true, 'Foo');

        $contents = '';

        if (false === $scoped) {
            $this->decoratedScoperProphecy->scope(Argument::cetera())->willReturn($expected = 'scoped by decorated scoper');
            $scopedCount = 1;
        } else {
            $expected = $contents;
            $scopedCount = 0;
        }

        $actual = $this->scoper->scope($file, $contents, $prefix, $patchers, $whitelist);

        $this->assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes($scopedCount);
    }

    /**
     * @dataProvider provideYamlFiles
     */
    public function test_it_scopes_Yaml_files(string $contents, string $expected): void
    {
        $prefix = 'Humbug';
        $file = 'file.yaml';
        $patchers = [create_fake_patcher()];
        $whitelist = Whitelist::create(true, true, true, 'Foo');

        $actual = $this->scoper->scope($file, $contents, $prefix, $patchers, $whitelist);

        $this->assertSame($expected, $actual);

        $this->decoratedScoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(0);
    }

    public function provideYamlFilesExtensions(): Generator
    {
        yield ['file.yaml', true];
        yield ['file.yml', true];
        yield ['file.YAML', true];
        yield ['file.YML', true];
        yield ['file.yam', false];
        yield ['file.aml', false];
        yield ['file', false];
    }

    public function provideYamlFiles(): Generator
    {
        yield ['', ''];

        yield [
            <<<'YAML'
services:
    Symfony\Component\Console\Style\SymfonyStyle: ~
    Symfony\Component\Console\Input\InputInterface:
        alias: 'Symfony\Component\Console\Input\ArgvInput'
    Symfony\Component\Console\Output\OutputInterface: '@Symfony\Component\Console\Output\ConsoleOutput'
YAML
            ,
            <<<'YAML'
services:
    Humbug\Symfony\Component\Console\Style\SymfonyStyle: ~
    Humbug\Symfony\Component\Console\Input\InputInterface:
        alias: 'Humbug\Symfony\Component\Console\Input\ArgvInput'
    Humbug\Symfony\Component\Console\Output\OutputInterface: '@Humbug\Symfony\Component\Console\Output\ConsoleOutput'
YAML
        ];

        yield [
            <<<'YAML'
services:
    "Symfony\\Component\\Console\\Style\\SymfonyStyle": ~
    "Symfony\\Component\\Console\\Input\\InputInterface":
        alias: "Symfony\\Component\\Console\\Input\\ArgvInput"
    "Symfony\\Component\\Console\\Output\\OutputInterface": "@Symfony\\Component\\Console\\Output\\ConsoleOutput"
YAML
            ,
            <<<'YAML'
services:
    "Humbug\\Symfony\\Component\\Console\\Style\\SymfonyStyle": ~
    "Humbug\\Symfony\\Component\\Console\\Input\\InputInterface":
        alias: "Humbug\\Symfony\\Component\\Console\\Input\\ArgvInput"
    "Humbug\\Symfony\\Component\\Console\\Output\\OutputInterface": "@Humbug\\Symfony\\Component\\Console\\Output\\ConsoleOutput"
YAML
        ];

        yield [
            <<<'YAML'
services:
    "Symfony\\Component\\Console\\Style\\SymfonyStyle": ~
    "Symfony\\Component\\Console\\Input\\InputInterface": '@Symfony\Component\Console\Style\SymfonyStyle'
YAML
            ,
            <<<'YAML'
services:
    "Humbug\\Symfony\\Component\\Console\\Style\\SymfonyStyle": ~
    "Humbug\\Symfony\\Component\\Console\\Input\\InputInterface": '@Humbug\Symfony\Component\Console\Style\SymfonyStyle'
YAML
        ];

        yield [
            <<<'YAML'
services:
    Acme\Controller\:
        resource: "../src"
YAML
            ,
            <<<'YAML'
services:
    Humbug\Acme\Controller\:
        resource: "../src"
YAML
        ];

        yield [
            <<<'YAML'
services:
    Acme\Foo: '@Acme\Foo\Bar'
    Acme\Foo: '@Acme\Bar\Acme\Foo'
YAML
            ,
            <<<'YAML'
services:
    Humbug\Acme\Foo: '@Humbug\Acme\Foo\Bar'
    Humbug\Acme\Foo: '@Humbug\Acme\Bar\Acme\Foo'
YAML
        ];

        yield [
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

YAML
            ,
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

YAML
        ];
    }
}
