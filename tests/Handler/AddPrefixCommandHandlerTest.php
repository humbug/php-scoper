<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\PhpScoper\Tests\Handler;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Webmozart\Console\Api\Command\Command;
use Webmozart\Console\Args\ArgvArgs;
use Webmozart\Console\ConsoleApplication;
use Webmozart\Console\Formatter\PlainFormatter;
use Webmozart\PhpScoper\Handler\AddPrefixCommandHandler;
use Webmozart\PhpScoper\PhpScoperApplicationConfig;
use Webmozart\PhpScoper\Tests\Handler\Util\NormalizedLineEndingsIO;
use Webmozart\PhpScoper\Tests\TestUtil;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AddPrefixCommandHandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private static $application;

    /**
     * @var Formatter
     */
    private static $formatter;

    /**
     * @var Command
     */
    private static $command;

    /**
     * @var NormalizedLineEndingsIO
     */
    private $io;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @var AddPrefixCommandHandler
     */
    private $handler;

    public static function setUpBeforeClass()
    {
        self::$application = new ConsoleApplication(new PhpScoperApplicationConfig());
        self::$formatter = new PlainFormatter(self::$application->getConfig()->getStyleSet());
        self::$command = self::$application->getCommand('add-prefix');
    }

    protected function setUp()
    {
        $filesystem = new Filesystem();
        $finder = new Finder();
        $this->handler = new AddPrefixCommandHandler($filesystem, $finder);
        $this->io = new NormalizedLineEndingsIO('', self::$formatter);
        $this->tempDir = TestUtil::makeTempDir('php-scoper', __CLASS__);

        $filesystem->mirror(__DIR__.'/../Fixtures/original', $this->tempDir);
    }

    protected function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->tempDir);
    }

    public function testAddPrefixToDirectory()
    {
        chdir($this->tempDir);

        $args = self::$command->parseArgs(new ArgvArgs(['add-prefix', 'MyPrefix\\', 'dir']));

        $expected = <<<EOF
...

EOF;

        $this->assertSame(0, $this->handler->handle($args, $this->io));
        $this->assertSame($expected, $this->io->fetchOutput());
        $this->assertEmpty($this->io->fetchErrors());

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/MyClass.php',
            $this->tempDir.'/dir/MyClass.php'
        );

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/MySecondClass.php',
            $this->tempDir.'/dir/MySecondClass.php'
        );

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/dir/MyThirdClass.php',
            $this->tempDir.'/dir/dir/MyThirdClass.php'
        );
    }

    public function testAddPrefixToDirectoryShouldIgnoreNotPHPFiles()
    {
        chdir($this->tempDir);

        $args = self::$command->parseArgs(new ArgvArgs(['add-prefix', 'MyPrefix\\', 'dir']));

        $expected = <<<EOF
...

EOF;

        $this->assertSame(0, $this->handler->handle($args, $this->io));
        $this->assertSame($expected, $this->io->fetchOutput());
        $this->assertEmpty($this->io->fetchErrors());

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/NotAPHPFile.txt',
            $this->tempDir.'/dir/NotAPHPFile.txt'
        );
    }

    public function testAddPrefixToFile()
    {
        chdir($this->tempDir);

        $args = self::$command->parseArgs(new ArgvArgs(['add-prefix', 'MyPrefix\\', 'dir/MyClass.php']));

        $expected = <<<EOF
...

EOF;

        $this->assertSame(0, $this->handler->handle($args, $this->io));
        $this->assertSame($expected, $this->io->fetchOutput());
        $this->assertEmpty($this->io->fetchErrors());

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/MyClass.php',
            $this->tempDir.'/dir/MyClass.php'
        );
    }

    public function testAddPrefixToMultiplePaths()
    {
        chdir($this->tempDir);

        $args = self::$command->parseArgs(new ArgvArgs(['add-prefix', 'MyPrefix\\', 'dir/MyClass.php', 'dir/MySecondClass.php']));

        $expected = <<<EOF
...

EOF;

        $this->assertSame(0, $this->handler->handle($args, $this->io));
        $this->assertSame($expected, $this->io->fetchOutput());
        $this->assertEmpty($this->io->fetchErrors());

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/MyClass.php',
            $this->tempDir.'/dir/MyClass.php'
        );

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/MySecondClass.php',
            $this->tempDir.'/dir/MySecondClass.php'
        );
    }
}
