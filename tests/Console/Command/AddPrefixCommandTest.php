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
use Humbug\PhpScoper\Container;
use Humbug\PhpScoper\FileSystemTestCase;
use Humbug\PhpScoper\Patcher\SymfonyPatcher;
use Humbug\PhpScoper\Scoper;
use Humbug\PhpScoper\Whitelist;
use InvalidArgumentException;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use RuntimeException as RootRuntimeException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Filesystem\Filesystem;
use function chdir;
use function count;
use function file_get_contents;
use function Humbug\PhpScoper\escape_path;
use function preg_replace;
use function realpath;
use function sprintf;
use const DIRECTORY_SEPARATOR;

/**
 * @covers \Humbug\PhpScoper\Console\Command\AddPrefixCommand
 */
class AddPrefixCommandTest extends FileSystemTestCase
{
    use ProphecyTrait;

    private const FIXTURE_PATH = __DIR__.'/../../../fixtures';

    /**
     * @var ApplicationTester
     */
    private $appTester;

    /**
     * @var Filesystem|ObjectProphecy
     */
    private $fileSystemProphecy;

    /**
     * @var Scoper|ObjectProphecy
     */
    private $scoperProphecy;

    /**
     * @inheritdoc
     */
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
  help        Display help for a command
  list        List commands

EOF;

        $actual = $this->appTester->getDisplay(true);
        $actual = preg_replace('/php-scoper-test version .*/', 'php-scoper-test version UNKNOWN', $actual);

        $this->assertSame($expected, $actual);
        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    public function test_get_version_menu(): void
    {
        $input = [
            '--version',
        ];

        $this->scoperProphecy->scope(Argument::cetera())->shouldNotBeCalled();

        $this->appTester->run($input);

        $expected = <<<'EOF'
php-scoper-test version UNKNOWN

EOF;

        $actual = $this->appTester->getDisplay(true);
        $actual = preg_replace('/php-scoper-test version .*/', 'php-scoper-test version UNKNOWN', $actual);

        $this->assertSame($expected, $actual);
        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    public function test_scope_the_given_paths(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                $root = self::FIXTURE_PATH.'/set002/original',
            ],
            '--output-dir' => $this->tmp,
            '--no-interaction',
            '--no-config' => null,
        ];

        $this->fileSystemProphecy->isAbsolutePath($root)->willReturn(true);
        $this->fileSystemProphecy->isAbsolutePath($this->tmp)->willReturn(true);

        $this->fileSystemProphecy->mkdir($this->tmp)->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();

        $expectedFiles = [
            'composer/installed.json' => 'f1',
            'file.php' => 'f2',
            'invalid-file.php' => 'f3',
            'scoper.inc.php' => 'f4',
        ];

        $root = realpath($root);

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = escape_path($root.'/'.$expectedFile);
            $outputPath = escape_path($this->tmp.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope(
                    $inputPath,
                    $inputContents,
                    'MyPrefix',
                    Argument::any(),
                    Whitelist::create(true, true, true)
                )
                ->willReturn($prefixedContents)
            ;

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(2);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotHaveBeenCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldHaveBeenCalled(count($expectedFiles));

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(count($expectedFiles));
    }

    public function test_let_the_file_unchanged_when_cannot_scope_a_file(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                $root = self::FIXTURE_PATH.'/set002/original',
            ],
            '--output-dir' => $this->tmp,
            '--no-interaction',
            '--no-config' => null,
        ];

        $this->fileSystemProphecy->isAbsolutePath($root)->willReturn(true);
        $this->fileSystemProphecy->isAbsolutePath($this->tmp)->willReturn(true);

        $this->fileSystemProphecy->mkdir($this->tmp)->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();

        $expectedFiles = [
            'composer/installed.json' => 'f1',
            'file.php' => 'f2',
            'invalid-file.php' => 'f3',
            'scoper.inc.php' => null,
        ];

        $root = realpath($root);

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = escape_path($root.'/'.$expectedFile);
            $outputPath = escape_path($this->tmp.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            if (null !== $prefixedContents) {
                $this->scoperProphecy
                    ->scope(
                        $inputPath,
                        $inputContents,
                        'MyPrefix',
                        Argument::any(),
                        Whitelist::create(true, true, true)
                    )
                    ->willReturn($prefixedContents)
                ;

                $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
            } else {
                $this->scoperProphecy
                    ->scope(
                        $inputPath,
                        $inputContents,
                        'MyPrefix',
                        Argument::any(),
                        Whitelist::create(true, true, true)
                    )
                    ->willThrow(new RootRuntimeException('Scoping of the file failed'))
                ;

                $this->fileSystemProphecy->dumpFile($outputPath, $inputContents)->shouldBeCalled();
            }
        }

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(2);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotHaveBeenCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldHaveBeenCalled(count($expectedFiles));

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(count($expectedFiles));
    }

    public function test_do_not_scope_duplicated_given_paths(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH.'/set002/original/file.php',
                self::FIXTURE_PATH.'/set002/original/file.php',
            ],
            '--output-dir' => $this->tmp,
            '--no-interaction',
            '--no-config' => null,
        ];

        $root = realpath(self::FIXTURE_PATH.'/set002/original');

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);

        $this->fileSystemProphecy->mkdir($this->tmp)->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();

        $expectedFiles = [
            'file.php' => 'f1',
        ];

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = escape_path($root.'/'.$expectedFile);
            $outputPath = escape_path($this->tmp.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope(
                    $inputPath,
                    $inputContents,
                    'MyPrefix',
                    Argument::any(),
                    Whitelist::create(true, true, true)
                )
                ->willReturn($prefixedContents)
            ;

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(3);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotHaveBeenCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldHaveBeenCalled(count($expectedFiles));

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(count($expectedFiles));
    }

    public function test_scope_the_given_paths_and_the_ones_found_by_the_finder(): void
    {
        chdir($rootPath = escape_path(self::FIXTURE_PATH.'/set012'));

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                self::FIXTURE_PATH.'/set002/original/file.php',
            ],
            '--output-dir' => $this->tmp,
            '--no-interaction',
        ];

        $this->fileSystemProphecy->isAbsolutePath('scoper.inc.php')->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);

        $this->fileSystemProphecy->mkdir($this->tmp)->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();

        $expectedFiles = [
            self::FIXTURE_PATH.'/set002/original/file.php' => $this->tmp.'/set002/original/file.php',
            $rootPath.'/dir/file1.php' => $this->tmp.'/set012/dir/file1.php',
            $rootPath.'/dir/file2.php' => $this->tmp.'/set012/dir/file2.php',
        ];

        foreach ($expectedFiles as $inputPath => $outputPath) {
            $inputPath = realpath($inputPath);
            $outputPath = escape_path($outputPath);

            $inputContents = file_get_contents($inputPath);
            $prefixedFileContents = 'Random string';

            $this->scoperProphecy
                ->scope(
                    $inputPath,
                    $inputContents,
                    'MyPrefix',
                    Argument::any(),
                    Whitelist::create(true, true, true)
                )
                ->willReturn($prefixedFileContents)
            ;

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedFileContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(3);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotHaveBeenCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldHaveBeenCalled(count($expectedFiles));

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(count($expectedFiles));
    }

    public function test_applies_a_random_prefix_when_none_given(): void
    {
        $input = [
            'add-prefix',
            'paths' => [
                self::FIXTURE_PATH.'/set002/original',
            ],
            '--output-dir' => $this->tmp,
            '--no-interaction',
            '--no-config' => null,
        ];

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldBeCalled();

        $this->scoperProphecy
            ->scope(
                Argument::any(),
                Argument::any(),
                Argument::that(
                    function (string $prefix): bool {
                        $this->assertMatchesRegularExpression(
                            '/^\_PhpScoper[a-z0-9]{12}$/',
                            $prefix
                        );

                        return true;
                    }
                ),
                Argument::any(),
                Whitelist::create(true, true, true)
            )
            ->willReturn('')
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldHaveBeenCalled();
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalled();
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotHaveBeenCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldHaveBeenCalled();

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalled();
    }

    public function test_scope_the_current_working_directory_if_no_path_given(): void
    {
        chdir($root = self::FIXTURE_PATH.'/set002/original');

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            '--output-dir' => $this->tmp,
            '--no-interaction',
            '--no-config' => null,
        ];

        $this->fileSystemProphecy->isAbsolutePath($root)->willReturn(true);
        $this->fileSystemProphecy->isAbsolutePath($this->tmp)->willReturn(true);

        $this->fileSystemProphecy->mkdir($this->tmp)->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();

        $expectedFiles = [
            'composer/installed.json' => 'f1',
            'file.php' => 'f2',
            'invalid-file.php' => 'f3',
            'scoper.inc.php' => 'f4',
        ];

        $root = realpath($root);

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = escape_path($root.'/'.$expectedFile);
            $outputPath = escape_path($this->tmp.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope(
                    $inputPath,
                    $inputContents,
                    'MyPrefix',
                    Argument::any(),
                    Whitelist::create(true, true, true)
                )
                ->willReturn($prefixedContents)
            ;

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotHaveBeenCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldHaveBeenCalled(count($expectedFiles));

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(count($expectedFiles));
    }

    public function test_prefix_can_end_by_a_backslash(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix\\',
            'paths' => [
                self::FIXTURE_PATH.'/set002/original',
            ],
            '--output-dir' => $this->tmp,
            '--no-interaction',
            '--no-config' => null,
        ];

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldBeCalled();

        $this->scoperProphecy
            ->scope(
                Argument::any(),
                Argument::any(),
                'MyPrefix',
                Argument::any(),
                Whitelist::create(true, true, true)
            )
            ->willReturn('')
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldHaveBeenCalled();
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalled();
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotHaveBeenCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldHaveBeenCalled();

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalled();
    }

    public function test_prefix_can_end_by_multiple_backslashes(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix\\\\',
            'paths' => [
                self::FIXTURE_PATH.'/set002/original',
            ],
            '--output-dir' => $this->tmp,
            '--no-interaction',
            '--no-config' => null,
        ];

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldBeCalled();

        $this->scoperProphecy
            ->scope(
                Argument::any(),
                Argument::any(),
                'MyPrefix',
                Argument::any(),
                Whitelist::create(true, true, true)
            )
            ->willReturn('')
        ;

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldHaveBeenCalled();
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalled();
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotHaveBeenCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldHaveBeenCalled();

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalled();
    }

    public function test_an_output_directory_can_be_given(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                $root = self::FIXTURE_PATH.'/set002/original',
            ],
            '--output-dir' => $outDir = $this->tmp.DIRECTORY_SEPARATOR.'output-dir',
            '--no-interaction',
            '--no-config' => null,
        ];

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);

        $this->fileSystemProphecy->mkdir($outDir)->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();

        $expectedFiles = [
            'composer/installed.json' => 'f1',
            'file.php' => 'f2',
            'invalid-file.php' => 'f3',
            'scoper.inc.php' => 'f4',
        ];

        $root = realpath($root);

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = escape_path($root.'/'.$expectedFile);
            $outputPath = escape_path($outDir.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope(
                    $inputPath,
                    $inputContents,
                    'MyPrefix',
                    Argument::any(),
                    Whitelist::create(true, true, true)
                )
                ->willReturn($prefixedContents)
            ;

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(2);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotHaveBeenCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldHaveBeenCalled(count($expectedFiles));

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(count($expectedFiles));
    }

    public function test_relative_output_directory_are_made_absolute(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                $root = self::FIXTURE_PATH.'/set002/original',
            ],
            '--output-dir' => $outDir = 'output-dir',
            '--no-interaction',
            '--no-config' => null,
        ];

        $this->fileSystemProphecy->isAbsolutePath($outDir)->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);

        $this->fileSystemProphecy->mkdir($this->tmp.DIRECTORY_SEPARATOR.$outDir)->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();

        $expectedFiles = [
            'composer/installed.json' => 'f1',
            'file.php' => 'f2',
            'invalid-file.php' => 'f3',
            'scoper.inc.php' => 'f4',
        ];

        $root = realpath($root);

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = escape_path($root.'/'.$expectedFile);
            $outputPath = escape_path($this->tmp.DIRECTORY_SEPARATOR.$outDir.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope(
                    $inputPath,
                    $inputContents,
                    'MyPrefix',
                    Argument::any(),
                    Whitelist::create(true, true, true)
                )
                ->willReturn($prefixedContents)
            ;

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(2);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotHaveBeenCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldHaveBeenCalled(count($expectedFiles));

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(count($expectedFiles));
    }

    public function test_throws_an_error_when_passing_a_non_existent_config(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            '--config' => 'unknown',
            '--no-interaction',
        ];

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);

        $this->scoperProphecy->scope(Argument::cetera())->shouldNotBeCalled();

        try {
            $this->appTester->run($input);

            $this->fail('Expected exception to be thrown.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                sprintf(
                    'Could not find the configuration file "%sunknown".',
                    $this->tmp.DIRECTORY_SEPARATOR
                ),
                $exception->getMessage()
            );
        }

        $this->scoperProphecy->scope(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    public function test_attempts_to_use_patch_file_in_current_directory(): void
    {
        chdir(escape_path($root = self::FIXTURE_PATH.'/set006'));

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            '--output-dir' => $this->tmp,
            '--no-interaction',
        ];

        $this->fileSystemProphecy->isAbsolutePath($this->tmp)->willReturn(true);
        $this->fileSystemProphecy->isAbsolutePath('scoper.inc.php')->willReturn(false);

        $this->fileSystemProphecy->mkdir($this->tmp)->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);

        $expectedFiles = [
            'scoper.inc.php' => 'f1',
        ];

        $root = realpath($root);

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = escape_path($root.'/'.$expectedFile);
            $outputPath = escape_path($this->tmp.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope(
                    $inputPath,
                    $inputContents,
                    'MyPrefix',
                    Argument::that(static function ($arg) use (&$patchersFound) {
                        $patchersFound = $arg;

                        return true;
                    }),
                    Whitelist::create(true, true, true)
                )
                ->willReturn($prefixedContents)
            ;

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->assertCount(2, $patchersFound);
        $this->assertEquals(new SymfonyPatcher(), $patchersFound[0]);
        $this->assertEquals('Hello world!', $patchersFound[1]());

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(2);

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(count($expectedFiles));
    }

    public function test_throws_an_error_if_patch_file_returns_an_array_with_invalid_values(): void
    {
        chdir(escape_path(self::FIXTURE_PATH.'/set009'));

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            '--no-interaction',
        ];

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);

        $this->scoperProphecy->scope(Argument::cetera())->shouldNotBeCalled();

        try {
            $this->appTester->run($input);

            $this->fail('Expected exception to be thrown.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame(
                'Expected patchers to be an array of callables, the "0" element is not.',
                $exception->getMessage()
            );
        }

        $this->scoperProphecy->scope(Argument::cetera())->shouldNotHaveBeenCalled();
    }

    public function test_can_scope_projects_with_invalid_files(): void
    {
        chdir(escape_path($root = self::FIXTURE_PATH.'/set010'));

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            '--output-dir' => $this->tmp,
            '--no-interaction',
            '--no-config' => null,
        ];

        $this->fileSystemProphecy->isAbsolutePath($this->tmp)->willReturn(true);

        $this->fileSystemProphecy->mkdir($this->tmp)->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();

        $expectedFiles = [
            'invalid-json.json' => 'f1',
        ];

        $root = realpath($root);

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = escape_path($root.'/'.$expectedFile);
            $outputPath = escape_path($this->tmp.'/'.$expectedFile);

            $fileContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope(
                    $inputPath,
                    $fileContents,
                    'MyPrefix',
                    Argument::any(),
                    Whitelist::create(true, true, true)
                )
                ->willThrow($scopingException = new RuntimeException('Could not scope file'))
            ;

            $this->fileSystemProphecy->dumpFile($outputPath, $fileContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        $this->assertSame(0, $this->appTester->getStatusCode());

        $this->fileSystemProphecy->mkdir(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->exists(Argument::cetera())->shouldHaveBeenCalledTimes(1);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotHaveBeenCalled();
        $this->fileSystemProphecy->dumpFile(Argument::cetera())->shouldHaveBeenCalled(count($expectedFiles));

        $this->scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(count($expectedFiles));
    }

    private function createAppTester(): ApplicationTester
    {
        /** @var Filesystem $fileSystem */
        $fileSystem = $this->fileSystemProphecy->reveal();

        /** @var Scoper $handle */
        $handle = $this->scoperProphecy->reveal();

        $application = new Application(new Container(), 'php-scoper-test');
        $application->addCommands([
            new AddPrefixCommand($fileSystem, $handle),
        ]);
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        return new ApplicationTester($application);
    }
}
