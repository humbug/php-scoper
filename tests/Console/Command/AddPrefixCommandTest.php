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
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Filesystem\Filesystem;
use function Humbug\PhpScoper\escape_path;
use function Humbug\PhpScoper\make_tmp_dir;

/**
 * @covers \Humbug\PhpScoper\Console\Command\AddPrefixCommand
 */
class AddPrefixCommandTest extends TestCase
{
    const FIXTURE_PATH = __DIR__.'/../../../fixtures';
    
    /**
     * @var ApplicationTester
     */
    private $appTester;

    /**
     * @var string
     */
    private $cwd;

    /**
     * @var string
     */
    private $tmp;

    /**
     * @var Filesystem|ObjectProphecy
     */
    private $fileSystemProphecy;

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

        $this->tmp = make_tmp_dir('scoper', __CLASS__);

        $this->fileSystemProphecy = $this->prophesize(Filesystem::class);

        $this->handleProphecy = $this->prophesize(HandleAddPrefix::class);

        $this->appTester = $this->createAppTester();
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

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldNotHaveBeenCalled();

        $this->handleProphecy->__invoke(Argument::cetera())->shouldNotHaveBeenCalled();
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

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldNotHaveBeenCalled();

        $this->handleProphecy->__invoke(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    public function test_scope_the_given_paths()
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                escape_path('/path/to/dir1'),
                escape_path('/path/to/dir2'),
                escape_path('/path/to/file'),
            ],
            '--output-dir' => $this->tmp,
            '--no-interaction',
        ];

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);

        $this->handleProphecy
            ->__invoke(
                'MyPrefix',
                [
                    escape_path('/path/to/dir1'),
                    escape_path('/path/to/dir2'),
                    escape_path('/path/to/file'),
                ],
                $this->tmp,
                Argument::type('array'),
                Argument::type(ConsoleLogger::class)
            )
            ->shouldBeCalled()
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(5);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_applies_a_random_prefix_when_none_given()
    {
        $input = [
            'add-prefix',
            'paths' => [
                escape_path('/path/to/dir1'),
                escape_path('/path/to/dir2'),
                escape_path('/path/to/file'),
            ],
            '--output-dir' => $this->tmp,
        ];

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);

        $this->handleProphecy
            ->__invoke(
                Argument::that(
                    function (string $prefix): bool {
                        $this->assertRegExp(
                            '/^PhpScoper[a-z0-9]{13}$/',
                            $prefix
                        );

                        return true;
                    }
                ),
                [
                    escape_path('/path/to/dir1'),
                    escape_path('/path/to/dir2'),
                    escape_path('/path/to/file'),
                ],
                $this->tmp,
                Argument::type('array'),
                Argument::type(ConsoleLogger::class)
            )
            ->shouldBeCalled()
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(5);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_scope_the_current_working_directory_if_no_path_given()
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            '--output-dir' => $this->tmp,
        ];

        $this->fileSystemProphecy->isAbsolutePath($this->tmp)->willReturn(true);
        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->willReturn(false);
        $this->fileSystemProphecy->exists($this->tmp)->willReturn(false);

        $this->handleProphecy
            ->__invoke(
                'MyPrefix',
                [
                    $this->cwd,
                ],
                $this->tmp,
                Argument::type('array'),
                Argument::type(ConsoleLogger::class)
            )
            ->shouldBeCalled()
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(2);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_relative_paths_are_relative_to_the_current_working_directory()
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                $path0 = escape_path('/path/to/dir1'),
                $path1 = escape_path('relative-path/to/dir2'),
                $path2 = escape_path('relative-path/to/file'),
            ],
            '--output-dir' => $this->tmp,
        ];

        $this->fileSystemProphecy->isAbsolutePath($path0)->willReturn(true);
        $this->fileSystemProphecy->isAbsolutePath($path1)->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath($path2)->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath($this->tmp)->willReturn(true);
        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->willReturn(false);
        $this->fileSystemProphecy->exists($this->tmp)->willReturn(false);

        $this->handleProphecy
            ->__invoke(
                'MyPrefix',
                [
                    escape_path('/path/to/dir1'),
                    escape_path($this->cwd.'/relative-path/to/dir2'),
                    escape_path($this->cwd.'/relative-path/to/file'),
                ],
                $this->tmp,
                Argument::type('array'),
                Argument::type(ConsoleLogger::class)
            )
            ->shouldBeCalled()
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(5);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_prefix_can_end_by_a_backslash()
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix\\',
            'paths' => [
                escape_path('/path/to/dir1'),
                escape_path('/path/to/dir2'),
                escape_path('/path/to/file'),
            ],
            '--output-dir' => $this->tmp,
        ];

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->exists($this->tmp)->willReturn(false);

        $this->handleProphecy
            ->__invoke(
                'MyPrefix',
                [
                    escape_path('/path/to/dir1'),
                    escape_path('/path/to/dir2'),
                    escape_path('/path/to/file'),
                ],
                $this->tmp,
                Argument::type('array'),
                Argument::type(ConsoleLogger::class)
            )
            ->shouldBeCalled()
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(5);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_prefix_can_end_by_multiple_backslashes()
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix\\\\',
            'paths' => [
                escape_path('/path/to/dir1'),
                escape_path('/path/to/dir2'),
                escape_path('/path/to/file'),
            ],
            '--output-dir' => $this->tmp,
        ];

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->exists($this->tmp)->willReturn(false);

        $this->handleProphecy
            ->__invoke(
                'MyPrefix',
                [
                    escape_path('/path/to/dir1'),
                    escape_path('/path/to/dir2'),
                    escape_path('/path/to/file'),
                ],
                $this->tmp,
                Argument::type('array'),
                Argument::type(ConsoleLogger::class)
            )
            ->shouldBeCalled()
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(5);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_an_output_directory_can_be_given()
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                escape_path('/path/to/dir1'),
            ],
            '--output-dir' => $outDir = $this->tmp.DIRECTORY_SEPARATOR.'output-dir',
        ];

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->exists($outDir)->willReturn(false);

        $this->handleProphecy
            ->__invoke(
                'MyPrefix',
                [
                    escape_path('/path/to/dir1'),
                ],
                $outDir,
                Argument::type('array'),
                Argument::type(ConsoleLogger::class)
            )
            ->shouldBeCalled()
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(3);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_relative_output_directory_are_made_absolute()
    {
        chdir($this->tmp);

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                escape_path('/path/to/dir1'),
            ],
            '--output-dir' => 'output-dir',
        ];

        $expectedOutputDir = $this->tmp.DIRECTORY_SEPARATOR.'output-dir';

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath('output-dir')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->exists($expectedOutputDir)->willReturn(false);

        $this->handleProphecy
            ->__invoke(
                'MyPrefix',
                [
                    escape_path('/path/to/dir1'),
                ],
                $expectedOutputDir,
                [],
                Argument::type(ConsoleLogger::class)
            )
            ->shouldBeCalled()
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(3);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    /**
     * @dataProvider provideEmptyPrefixes
     */
    public function test_cannot_apply_an_empty_prefix(string $prefix)
    {
        $input = [
            'add-prefix',
            '--prefix' => $prefix,
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
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                escape_path('/path/to/dir1'),
            ],
        ];

        $this->fileSystemProphecy->isAbsolutePath('php-scoper.php')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->exists('build')->willReturn(false);

        $this->handleProphecy
            ->__invoke(Argument::cetera())
            ->willThrow(
                $exception = new ScopingRuntimeException('Foo')
            )
        ;

        try {
            $this->appTester->run($input);

            $this->fail('Expected exception to be thrown.');
        } catch (ScopingRuntimeException $caughtException) {
            $this->assertSame($caughtException, $exception);
        }

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_throws_an_error_when_passing_a_non_existent_path_file()
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            '--patch-file' => 'unknown',
            'paths' => [
                escape_path('/path/to/dir1'),
            ],
        ];

        $this->fileSystemProphecy->isAbsolutePath('unknown')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->exists('build')->willReturn(false);

        $this->handleProphecy->__invoke(Argument::cetera())->shouldNotBeCalled();

        try {
            $this->appTester->run($input);

            $this->fail('Expected exception to be thrown.');
        } catch (RuntimeException $exception) {
            $patchFile = escape_path($this->cwd.'/unknown');

            $this->assertSame(
                "Could not find the file \"$patchFile\".",
                $exception->getMessage()
            );
            $this->assertSame(0, $exception->getCode());
            $this->assertNull($exception->getPrevious());
        }
    }

    public function test_attemps_to_use_patch_file_in_current_directory()
    {
        chdir(escape_path(self::FIXTURE_PATH.'/set006'));
        
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                escape_path('/path/to/dir1'),
            ],
        ];

        $this->fileSystemProphecy->isAbsolutePath('unknown')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->exists('build')->willReturn(false);

        $patchersFound = [];
        $this->handleProphecy
            ->__invoke(
                Argument::any(),
                Argument::any(),
                Argument::any(),
                Argument::that(function ($arg) use (&$patchersFound) {
                    $patchersFound = $arg;

                    return true;
                }),
                Argument::any()
            )
            ->shouldBeCalled();

        $this->appTester->run($input);

        $this->assertCount(1, $patchersFound);
        $this->assertEquals('Hello world!', $patchersFound[0]());

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_do_no_apply_any_patcher_if_default_patcher_file_not_found()
    {
        chdir(escape_path(self::FIXTURE_PATH.'/set007'));

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                escape_path('/path/to/dir1'),
            ],
        ];

        $this->fileSystemProphecy->isAbsolutePath('unknown')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->exists('build')->willReturn(false);

        $patchersFound = [];
        $this->handleProphecy
            ->__invoke(
                Argument::any(),
                Argument::any(),
                Argument::any(),
                Argument::that(function ($arg) use (&$patchersFound) {
                    $patchersFound = $arg;

                    return true;
                }),
                Argument::any()
            )
            ->shouldBeCalled();

        $this->appTester->run($input);

        $this->assertCount(0, $patchersFound);

        $this->handleProphecy->__invoke(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_throws_an_error_if_patch_file_returns_an_array_with_invalid_values()
    {
        chdir(escape_path(self::FIXTURE_PATH.'/set009'));

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                escape_path('/path/to/dir1'),
            ],
        ];

        $this->fileSystemProphecy->isAbsolutePath('unknown')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->exists('build')->willReturn(false);

        $this->handleProphecy->__invoke(Argument::cetera())->shouldNotBeCalled();

        try {
            $this->appTester->run($input);

            $this->fail('Expected exception to be thrown.');
        } catch (RuntimeException $exception) {
            $patchFile = escape_path($this->cwd.'/unknown');

            $this->assertSame(
                'Expected patchers to be an array of callables, the "0" element is not.',
                $exception->getMessage()
            );
            $this->assertSame(0, $exception->getCode());
            $this->assertNull($exception->getPrevious());
        }
    }

    public function provideEmptyPrefixes()
    {
        yield 'empty' => [''];

        yield 'space only' => ['  '];

        yield 'backslashes' => ['\\'];

        yield '1 backslash' => ['\\'];

        yield '2 backslashes' => ['\\\\'];
    }

    private function createAppTester(): ApplicationTester
    {
        /** @var Filesystem $fileSystem */
        $fileSystem = $this->fileSystemProphecy->reveal();

        /** @var HandleAddPrefix $handle */
        $handle = $this->handleProphecy->reveal();

        $application = new Application('php-scoper-test');
        $application->addCommands([
            new AddPrefixCommand($fileSystem, $handle),
        ]);
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        return new ApplicationTester($application);
    }
}
