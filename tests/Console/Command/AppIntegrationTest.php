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

namespace Humbug\PhpScoper\Console\Command;

use Fidry\Console\Application\SymfonyApplication;
use Fidry\Console\Command\SymfonyCommand;
use Humbug\PhpScoper\Configuration\ConfigurationFactory;
use Humbug\PhpScoper\Configuration\RegexChecker;
use Humbug\PhpScoper\Configuration\SymbolsConfigurationFactory;
use Humbug\PhpScoper\Console\Application;
use Humbug\PhpScoper\Console\AppTesterAbilities;
use Humbug\PhpScoper\Console\AppTesterTestCase;
use Humbug\PhpScoper\Container;
use Humbug\PhpScoper\FileSystemTestCase;
use Humbug\PhpScoper\PhpParser\FakeParser;
use Humbug\PhpScoper\PhpParser\FakePrinter;
use Humbug\PhpScoper\Scoper\Scoper;
use Humbug\PhpScoper\Symbol\EnrichedReflectorFactory;
use Humbug\PhpScoper\Symbol\Reflector;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @coversNothing
 *
 * @group integration
 *
 * @internal
 */
class AppIntegrationTest extends FileSystemTestCase implements AppTesterTestCase
{
    use AppTesterAbilities;
    use ProphecyTrait;

    /**
     * @var ObjectProphecy<Filesystem>
     */
    private ObjectProphecy $fileSystemProphecy;

    /**
     * @var ObjectProphecy<Scoper>
     */
    private ObjectProphecy $scoperProphecy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileSystemProphecy = $this->prophesize(Filesystem::class);
        $this->scoperProphecy = $this->prophesize(Scoper::class);

        $this->appTester = $this->createAppTester();
    }

    public function test_get_help_menu(): void
    {
        $input = [];

        $this->scoperProphecy->scope(Argument::cetera())->shouldNotBeCalled();

        $this->appTester->run($input);

        $expected = <<<'EOF'

                ____  __  ______     _____
               / __ \/ / / / __ \   / ___/_________  ____  ___  _____
              / /_/ / /_/ / /_/ /   \__ \/ ___/ __ \/ __ \/ _ \/ ___/
             / ____/ __  / ____/   ___/ / /__/ /_/ / /_/ /  __/ /
            /_/   /_/ /_/_/       /____/\___/\____/ .___/\___/_/
                                                 /_/

            PhpScoper version TestVersion 28/01/2020

            Usage:
              command [options] [arguments]

            Options:
              -h, --help            Display help for the given command. When no command is given display help for the list command
              -q, --quiet           Do not output any message
              -V, --version         Display this application version
                  --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
              -n, --no-interaction  Do not ask any interactive question
              -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

            Available commands:
              add-prefix      Goes through all the PHP files found in the given paths to apply the given prefix to namespaces & FQNs.
              completion      Dump the shell completion script
              help            Display help for a command
              init            Generates a configuration file.
              inspect         Outputs the processed file content based on the configuration.
              inspect-symbol  Checks the given symbol for a given configuration. Helpful to have an insight on how PHP-Scoper will interpret this symbol
              list            List commands

            EOF;

        $this->assertExpectedOutput($expected, 0);

        $this->fileSystemProphecy
            ->isAbsolutePath(Argument::cetera())
            ->shouldNotHaveBeenCalled();
    }

    public function test_get_version_menu(): void
    {
        $input = [
            '--version',
        ];

        $this->scoperProphecy->scope(Argument::cetera())->shouldNotBeCalled();

        $this->appTester->run($input);

        $expected = <<<'EOF'
            PhpScoper version TestVersion 28/01/2020

            EOF;

        $this->assertExpectedOutput($expected, 0);

        $this->fileSystemProphecy
            ->isAbsolutePath(Argument::cetera())
            ->shouldNotHaveBeenCalled();
    }

    private function createAppTester(): ApplicationTester
    {
        /** @var Filesystem $fileSystem */
        $fileSystem = $this->fileSystemProphecy->reveal();

        /** @var Scoper $scoper */
        $scoper = $this->scoperProphecy->reveal();

        $application = new SymfonyApplication(
            $innerApp = new Application(
                new Container(),
                'TestVersion',
                '28/01/2020',
                false,
                false,
            ),
        );
        $application->add(
            new SymfonyCommand(
                new AddPrefixCommand(
                    $fileSystem,
                    new DummyScoperFactory(
                        new FakeParser(),
                        new EnrichedReflectorFactory(Reflector::createEmpty()),
                        new FakePrinter(),
                        $scoper,
                    ),
                    $innerApp,
                    new ConfigurationFactory(
                        $fileSystem,
                        new SymbolsConfigurationFactory(
                            new RegexChecker(),
                        ),
                    ),
                ),
            ),
        );

        return new ApplicationTester($application);
    }
}
