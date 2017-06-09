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

use Humbug\PhpScoper\Console\Application;
use Humbug\PhpScoper\Handler\HandleAddPrefix;
use Humbug\PhpScoper\Logger\ConsoleLogger;
use Humbug\PhpScoper\Throwable\Exception\RuntimeException as ScopingRuntimeException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Exception\RuntimeException as SymfonyConsoleRuntimeException;
use Symfony\Component\Console\Tester\ApplicationTester;
use function Humbug\PhpScoper\escape_path;

/**
 * @covers \Humbug\PhpScoper\Console\Command\AddPrefixCommand
 */
class AddPrefixCommandTest extends TestCase
{
    /**
     * @var ApplicationTester
     */
    private $appTester;

    /**
     * @var string
     */
    private $cwd;

    /**
     * @var HandleAddPrefix|ObjectProphecy
     */
    private $handleProphecy;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        if (null !== $this->appTester) {
            return;
        }

        $this->cwd = getcwd();

        $this->handleProphecy = $this->prophesize(HandleAddPrefix::class);

        $this->appTester = $this->createAppTester(false);
    }

    public function test_get_help_menu()
    {
        $input = [];

        $this->handleProphecy->__invoke(Argument::cetera())->shouldNotBeCalled();

        $this->appTester->run($input);

        $expected = <<<'EOF'

    ____  __  ______     _____                           
   / __ \/ / / / __ \   / ___/_________  ____  ___  _____
  / /_/ / /_/ / /_/ /   \__ \/ ___/ __ \/ __ \/ _ \/ ___/
 / ____/ __  / ____/   ___/ / /__/ /_/ / /_/ /  __/ /    
/_/   /_/ /_/_/       /____/\___/\____/ .___/\___/_/     
                                     /_/

php-scoper-test version UNKNOWN

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  add-prefix  Goes through all the PHP files found in the given paths to apply the given prefix to namespaces & FQNs.
  help        Displays help for a command
  list        Lists commands

EOF;

        $actual = $this->appTester->getDisplay(true);

        $this->assertSame($expected, $actual);
        $this->assertSame(0, $this->appTester->getStatusCode());
    }

    public function test_get_version_menu()
    {
        $input = [
            '--version',
        ];

        $this->handleProphecy->__invoke(Argument::cetera())->shouldNotBeCalled();

        $this->appTester->run($input);

        $expected = <<<'EOF'
php-scoper-test version UNKNOWN

EOF;

        $actual = $this->appTester->getDisplay(true);

        $this->assertSame($expected, $actual);
        $this->assertSame(0, $this->appTester->getStatusCode());
    }

    public function test_scope_the_given_paths()
    {
        $input = [
            'add-prefix',
            'prefix' => 'MyPrefix',
            'paths' => [
                escape_path('/path/to/dir1'),
                escape_path('/path/to/dir2'),
                escape_path('/path/to/file'),
            ],
        ];

        $this->handleProphecy
            ->__invoke(
                'MyPrefix',
                [
                    escape_path('/path/to/dir1'),
                    escape_path('/path/to/dir2'),
                    escape_path('/path/to/file'),
                ],
                Argument::type(ConsoleLogger::class)
            )
            ->shouldBeCalled()
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_relative_paths_are_relative_to_the_current_working_directory()
    {
        $input = [
            'add-prefix',
            'prefix' => 'MyPrefix',
            'paths' => [
                escape_path('/path/to/dir1'),
                escape_path('relative-path/to/dir2'),
                escape_path('relative-path/to/file'),
            ],
        ];

        $this->handleProphecy
            ->__invoke(
                'MyPrefix',
                [
                    escape_path('/path/to/dir1'),
                    escape_path($this->cwd.'/relative-path/to/dir2'),
                    escape_path($this->cwd.'/relative-path/to/file'),
                ],
                Argument::type(ConsoleLogger::class)
            )
            ->shouldBeCalled()
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_prefix_can_end_by_a_backslash()
    {
        $input = [
            'add-prefix',
            'prefix' => 'MyPrefix\\',
            'paths' => [
                escape_path('/path/to/dir1'),
                escape_path('/path/to/dir2'),
                escape_path('/path/to/file'),
            ],
        ];

        $this->handleProphecy
            ->__invoke(
                'MyPrefix',
                [
                    escape_path('/path/to/dir1'),
                    escape_path('/path/to/dir2'),
                    escape_path('/path/to/file'),
                ],
                Argument::type(ConsoleLogger::class)
            )
            ->shouldBeCalled()
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_prefix_can_end_by_multiple_backslashes()
    {
        $input = [
            'add-prefix',
            'prefix' => 'MyPrefix\\\\',
            'paths' => [
                escape_path('/path/to/dir1'),
                escape_path('/path/to/dir2'),
                escape_path('/path/to/file'),
            ],
        ];

        $this->handleProphecy
            ->__invoke(
                'MyPrefix',
                [
                    escape_path('/path/to/dir1'),
                    escape_path('/path/to/dir2'),
                    escape_path('/path/to/file'),
                ],
                Argument::type(ConsoleLogger::class)
            )
            ->shouldBeCalled()
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    /**
     * @dataProvider provideEmptyPrefixes
     */
    public function test_the_prefix_given_cannot_be_empty(string $prefix)
    {
        $input = [
            'add-prefix',
            'prefix' => $prefix,
            'paths' => [
                escape_path('/path/to/dir1'),
                escape_path('relative-path/to/dir2'),
                escape_path('relative-path/to/file'),
            ],
        ];

        $this->handleProphecy->__invoke(Argument::cetera())->shouldNotBeCalled();

        try {
            $this->appTester->run($input);

            $this->fail('Expected exception to be thrown.');
        } catch (SymfonyConsoleRuntimeException $exception) {
            $this->assertSame(
                'Expected "prefix" argument to be a non empty string.',
                $exception->getMessage()
            );
            $this->assertSame(0, $exception->getCode());
            $this->assertNull($exception->getPrevious());
        }
    }

    public function test_throws_an_error_when_scoping_fails()
    {
        $this->appTester = $this->createAppTester(true);

        $input = [
            'add-prefix',
            'prefix' => 'MyPrefix',
            'paths' => [
                escape_path('/path/to/dir1'),
                escape_path('/path/to/dir2'),
                escape_path('/path/to/file'),
            ],
        ];

        $this->handleProphecy
            ->__invoke(Argument::cetera())
            ->willThrow(
                $exception = new ScopingRuntimeException('Foo')
            )
        ;

        $this->appTester->run(
            $input,
            ['capture_stderr_separately' => true]
        );

        $this->assertNotEmpty($this->appTester->getErrorOutput(true));
        $this->assertSame(1, $this->appTester->getStatusCode());

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function provideEmptyPrefixes()
    {
        yield 'empty' => [''];

        yield 'space only' => ['  '];

        yield 'backslashes' => ['\\'];

        yield '1 backslash' => ['\\'];

        yield '2 backslashes' => ['\\\\'];
    }

    private function createAppTester(bool $catchExceptions): ApplicationTester
    {
        /** @var HandleAddPrefix $handle */
        $handle = $this->handleProphecy->reveal();

        $application = new Application('php-scoper-test');
        $application->addCommands([
            new AddPrefixCommand($handle),
        ]);
        $application->setAutoExit(false);
        $application->setCatchExceptions($catchExceptions);

        return new ApplicationTester($application);
    }
}
