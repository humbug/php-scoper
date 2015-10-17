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
        $this->handler = new AddPrefixCommandHandler($filesystem);
        $this->io = new NormalizedLineEndingsIO('', self::$formatter);
        $this->tempDir = TestUtil::makeTempDir('php-scoper', __CLASS__);

        $filesystem->mirror(__DIR__.'/../Fixtures/original/dir', $this->tempDir);
    }

    protected function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->tempDir);
    }

    public function testAddPrefixToDirectory()
    {
        chdir(dirname($this->tempDir));

        $args = self::$command->parseArgs(new ArgvArgs(['add-prefix', 'MyPrefix\\', 'dir']));

        $expected = <<<EOF
...

EOF;

        $this->assertSame(0, $this->handler->handle($args, $this->io));
        $this->assertSame($expected, $this->io->fetchOutput());
        $this->assertEmpty($this->io->fetchErrors());

        // check that the files in $this->tempDir match those in Fixtures/replaced/dir
    }

    public function testAddPrefixToFile()
    {
        chdir($this->tempDir);

        $args = self::$command->parseArgs(new ArgvArgs(['add-prefix', 'MyPrefix\\', 'MyClass.php']));

        $expected = <<<EOF
...

EOF;

        $this->assertSame(0, $this->handler->handle($args, $this->io));
        $this->assertSame($expected, $this->io->fetchOutput());
        $this->assertEmpty($this->io->fetchErrors());

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/MyClass.php',
            $this->tempDir.'/MyClass.php'
        );
    }

    public function testAddPrefixToMultiplePaths()
    {
        chdir($this->tempDir);

        $args = self::$command->parseArgs(new ArgvArgs(['add-prefix', 'MyPrefix\\', 'MyClass.php', 'MySecondClass.php']));

        $expected = <<<EOF
...

EOF;

        $this->assertSame(0, $this->handler->handle($args, $this->io));
        $this->assertSame($expected, $this->io->fetchOutput());
        $this->assertEmpty($this->io->fetchErrors());

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/MyClass.php',
            $this->tempDir.'/MyClass.php'
        );

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/MySecondClass.php',
            $this->tempDir.'/MySecondClass.php'
        );
    }
}
