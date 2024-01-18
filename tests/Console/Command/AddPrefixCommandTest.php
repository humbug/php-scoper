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

use Fidry\Console\Bridge\Application\SymfonyApplication;
use Fidry\Console\Bridge\Command\SymfonyCommand;
use Fidry\FileSystem\FS;
use Humbug\PhpScoper\Configuration\ConfigurationFactory;
use Humbug\PhpScoper\Configuration\RegexChecker;
use Humbug\PhpScoper\Configuration\SymbolsConfigurationFactory;
use Humbug\PhpScoper\Console\Application;
use Humbug\PhpScoper\Console\AppTesterAbilities;
use Humbug\PhpScoper\Console\AppTesterTestCase;
use Humbug\PhpScoper\Console\ConfigLoader;
use Humbug\PhpScoper\Console\ConsoleScoper;
use Humbug\PhpScoper\Container;
use Humbug\PhpScoper\FileSystemTestCase;
use Humbug\PhpScoper\PhpParser\FakeParser;
use Humbug\PhpScoper\PhpParser\FakePrinter;
use Humbug\PhpScoper\Scoper\Scoper;
use Humbug\PhpScoper\Symbol\EnrichedReflectorFactory;
use Humbug\PhpScoper\Symbol\Reflector;
use InvalidArgumentException;
use PhpParser\Error as PhpParserError;
use PHPUnit\Framework\Attributes\CoversClass;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Filesystem\Filesystem;
use function count;
use function Safe\chdir;
use function Safe\file_get_contents;
use function Safe\realpath;
use function sprintf;
use const DIRECTORY_SEPARATOR;

/**
 * @internal
 */
#[CoversClass(AddPrefixCommand::class)]
#[CoversClass(ConfigLoader::class)]
#[CoversClass(ConsoleScoper::class)]
class AddPrefixCommandTest extends FileSystemTestCase implements AppTesterTestCase
{
    use AppTesterAbilities;
    use ProphecyTrait;

    private const FIXTURE_PATH = __DIR__.'/../../../fixtures';

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
        $this->fileSystemProphecy
            ->isAbsolutePath('scoper.inc.php')
            ->willReturn(false);

        $this->scoperProphecy = $this->prophesize(Scoper::class);

        $this->appTester = $this->createAppTester();
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
            'composer/installed.json' => '{"packages": []}}',
            'executable-file.php' => 'f5',
            'file.php' => 'f2',
            'invalid-file.php' => 'f3',
            'scoper.inc.php' => 'f4',
        ];

        $root = realpath($root);

        $this->fileSystemProphecy
            ->chmod(
                $this->tmp.'/executable-file.php',
                493,
            )
            ->shouldBeCalled();

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = FS::escapePath($root.'/'.$expectedFile);
            $outputPath = FS::escapePath($this->tmp.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope(
                    $inputPath,
                    $inputContents,
                )
                ->willReturn($prefixedContents);

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        self::assertSame(0, $this->appTester->getStatusCode());

        $this->scoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes(count($expectedFiles));
    }

    public function test_let_the_file_unchanged_when_cannot_scope_a_file_but_is_marked_as_continue_on_failure(): void
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
            '--continue-on-failure' => null,
        ];

        $this->fileSystemProphecy->isAbsolutePath($root)->willReturn(true);
        $this->fileSystemProphecy->isAbsolutePath($this->tmp)->willReturn(true);

        $this->fileSystemProphecy->mkdir($this->tmp)->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();

        $expectedFiles = [
            'composer/installed.json' => 'f1',
            'executable-file.php' => 'f5',
            'file.php' => 'f2',
            'invalid-file.php' => 'f3',
            'scoper.inc.php' => null,
        ];

        $root = realpath($root);

        $this->fileSystemProphecy
            ->chmod(
                $this->tmp.'/executable-file.php',
                493,
            )
            ->shouldBeCalled();

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = FS::escapePath($root.'/'.$expectedFile);
            $outputPath = FS::escapePath($this->tmp.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            if (null !== $prefixedContents) {
                $this->scoperProphecy
                    ->scope($inputPath, $inputContents)
                    ->willReturn($prefixedContents);

                $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
            } else {
                $this->scoperProphecy
                    ->scope($inputPath, $inputContents)
                    ->willThrow(new RuntimeException('Scoping of the file failed'));

                $this->fileSystemProphecy->dumpFile($outputPath, $inputContents)->shouldBeCalled();
            }
        }

        $this->appTester->run($input);

        self::assertSame(0, $this->appTester->getStatusCode());

        $this->scoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes(count($expectedFiles));
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
            $inputPath = FS::escapePath($root.'/'.$expectedFile);
            $outputPath = FS::escapePath($this->tmp.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope($inputPath, $inputContents)
                ->willReturn($prefixedContents);

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        self::assertSame(0, $this->appTester->getStatusCode());

        $this->scoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes(count($expectedFiles));
    }

    public function test_scope_the_given_paths_and_the_ones_found_by_the_finder(): void
    {
        chdir($rootPath = FS::escapePath(self::FIXTURE_PATH.'/set012'));

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
            $outputPath = FS::escapePath($outputPath);

            $inputContents = file_get_contents($inputPath);
            $prefixedFileContents = 'Random string';

            $this->scoperProphecy
                ->scope($inputPath, $inputContents)
                ->willReturn($prefixedFileContents);

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedFileContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        self::assertSame(0, $this->appTester->getStatusCode());

        $this->scoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes(count($expectedFiles));
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
            'executable-file.php' => 'f5',
            'file.php' => 'f2',
            'invalid-file.php' => 'f3',
            'scoper.inc.php' => 'f4',
        ];

        $root = realpath($root);

        $this->fileSystemProphecy
            ->chmod(
                $this->tmp.'/executable-file.php',
                493,
            )
            ->shouldBeCalled();

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = FS::escapePath($root.'/'.$expectedFile);
            $outputPath = FS::escapePath($this->tmp.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope($inputPath, $inputContents)
                ->willReturn($prefixedContents);

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        self::assertSame(0, $this->appTester->getStatusCode());

        $this->scoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes(count($expectedFiles));
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
            'executable-file.php' => 'f5',
            'file.php' => 'f2',
            'invalid-file.php' => 'f3',
            'scoper.inc.php' => 'f4',
        ];

        $root = realpath($root);

        $this->fileSystemProphecy
            ->chmod(
                $outDir.'/executable-file.php',
                493,
            )
            ->shouldBeCalled();

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = FS::escapePath($root.'/'.$expectedFile);
            $outputPath = FS::escapePath($outDir.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope($inputPath, $inputContents)
                ->willReturn($prefixedContents);

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        self::assertSame(0, $this->appTester->getStatusCode());

        $this->scoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes(count($expectedFiles));
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
            'executable-file.php' => 'f5',
            'file.php' => 'f2',
            'invalid-file.php' => 'f3',
            'scoper.inc.php' => 'f4',
        ];

        $root = realpath($root);

        $this->fileSystemProphecy
            ->chmod(
                $this->tmp.DIRECTORY_SEPARATOR.$outDir.'/executable-file.php',
                493,
            )
            ->shouldBeCalled();

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = FS::escapePath($root.'/'.$expectedFile);
            $outputPath = FS::escapePath($this->tmp.DIRECTORY_SEPARATOR.$outDir.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope($inputPath, $inputContents)
                ->willReturn($prefixedContents);

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        self::assertSame(0, $this->appTester->getStatusCode());

        $this->scoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes(count($expectedFiles));
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

            self::fail('Expected exception to be thrown.');
        } catch (RuntimeException $exception) {
            self::assertSame(
                sprintf(
                    'Could not find the configuration file "%sunknown".',
                    $this->tmp.DIRECTORY_SEPARATOR,
                ),
                $exception->getMessage(),
            );
        }

        $this->scoperProphecy
            ->scope(Argument::cetera())
            ->shouldNotHaveBeenCalled();
    }

    public function test_throws_an_error_if_patch_file_returns_an_array_with_invalid_values(): void
    {
        chdir(FS::escapePath(self::FIXTURE_PATH.'/set009'));

        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            '--no-interaction',
        ];

        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);

        $this->scoperProphecy->scope(Argument::cetera())->shouldNotBeCalled();

        try {
            $this->appTester->run($input);

            self::fail('Expected exception to be thrown.');
        } catch (InvalidArgumentException $exception) {
            self::assertSame(
                'Expected patchers to be an array of callables, the "0" element is not.',
                $exception->getMessage(),
            );
        }

        $this->scoperProphecy
            ->scope(Argument::cetera())
            ->shouldNotHaveBeenCalled();
    }

    public function test_can_scope_projects_with_invalid_files(): void
    {
        chdir(FS::escapePath($root = self::FIXTURE_PATH.'/set010'));

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
        $this->fileSystemProphecy->chmod(Argument::cetera())->shouldNotBeCalled();

        $expectedFiles = [
            'invalid-json.json' => 'f1',
        ];

        $root = realpath($root);

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = FS::escapePath($root.'/'.$expectedFile);
            $outputPath = FS::escapePath($this->tmp.'/'.$expectedFile);

            $fileContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope($inputPath, $fileContents)
                ->willThrow(new PhpParserError('Could not scope file'));

            $this->fileSystemProphecy->dumpFile($outputPath, $fileContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        self::assertSame(0, $this->appTester->getStatusCode());

        $this->scoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes(count($expectedFiles));
    }

    public function test_it_outputs_in_the_build_directory_if_no_output_dir_is_given(): void
    {
        $input = [
            'add-prefix',
            '--prefix' => 'MyPrefix',
            'paths' => [
                $root = self::FIXTURE_PATH.'/set002/original',
            ],
            '--no-interaction',
            '--no-config' => null,
        ];

        $outDir = 'build';

        $this->fileSystemProphecy->isAbsolutePath($outDir)->willReturn(false);
        $this->fileSystemProphecy->isAbsolutePath(Argument::cetera())->willReturn(true);

        $this->fileSystemProphecy->mkdir($this->tmp.DIRECTORY_SEPARATOR.$outDir)->shouldBeCalled();
        $this->fileSystemProphecy->exists(Argument::cetera())->willReturn(false);
        $this->fileSystemProphecy->remove(Argument::cetera())->shouldNotBeCalled();

        $expectedFiles = [
            'composer/installed.json' => 'f1',
            'executable-file.php' => 'f5',
            'file.php' => 'f2',
            'invalid-file.php' => 'f3',
            'scoper.inc.php' => 'f4',
        ];

        $root = realpath($root);

        $this->fileSystemProphecy
            ->chmod(
                $this->tmp.DIRECTORY_SEPARATOR.$outDir.'/executable-file.php',
                493,
            )
            ->shouldBeCalled();

        foreach ($expectedFiles as $expectedFile => $prefixedContents) {
            $inputPath = FS::escapePath($root.'/'.$expectedFile);
            $outputPath = FS::escapePath($this->tmp.DIRECTORY_SEPARATOR.$outDir.'/'.$expectedFile);

            $inputContents = file_get_contents($inputPath);

            $this->scoperProphecy
                ->scope($inputPath, $inputContents)
                ->willReturn($prefixedContents);

            $this->fileSystemProphecy->dumpFile($outputPath, $prefixedContents)->shouldBeCalled();
        }

        $this->appTester->run($input);

        self::assertSame(0, $this->appTester->getStatusCode());

        $this->scoperProphecy
            ->scope(Argument::cetera())
            ->shouldHaveBeenCalledTimes(count($expectedFiles));
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
                        new EnrichedReflectorFactory(
                            Reflector::createEmpty(),
                        ),
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
