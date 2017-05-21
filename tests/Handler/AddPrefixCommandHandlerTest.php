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

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Console\Api\Command\Command;
use Webmozart\Console\Args\StringArgs;
use Webmozart\Console\ConsoleApplication;
use Webmozart\Console\Formatter\PlainFormatter;
use Webmozart\PhpScoper\Handler\AddPrefixCommandHandler;
use Webmozart\PhpScoper\PhpScoperApplicationConfig;
use Webmozart\PhpScoper\Tests\Handler\Util\NormalizedLineEndingsIO;
use Webmozart\PhpScoper\Tests\TestUtil;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AddPrefixCommandHandlerTest extends TestCase
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
    private $workingDirectory;

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
        $this->handler = new AddPrefixCommandHandler();
        $this->io = new NormalizedLineEndingsIO('', self::$formatter);
        $this->workingDirectory = getcwd();
        $this->tempDir = TestUtil::makeTempDir('php-scoper', __CLASS__);

        $filesystem = new Filesystem();
        $filesystem->mirror(__DIR__.'/../Fixtures/original', $this->tempDir);
    }

    protected function tearDown()
    {
        chdir($this->workingDirectory);
        $filesystem = new Filesystem();
        $filesystem->remove($this->tempDir);
    }

    public function testAddPrefixToDirectory()
    {
        chdir($this->tempDir);

        $args = self::$command->parseArgs(new StringArgs('MyPrefix\\\\ dir'.DIRECTORY_SEPARATOR.'dir'));

        $expected = <<<EOF
Scoping $this->tempDir/dir/dir/MySecondClass.php. . . Success
Scoping $this->tempDir/dir/dir/MyThirdClass.php. . . Success
Scoping $this->tempDir/dir/dir/MyClass.php. . . Success

EOF;
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);

        $this->assertSame(0, $this->handler->handle($args, $this->io));
        $this->assertSame($expected, $this->io->fetchOutput());
        $this->assertEmpty($this->io->fetchErrors());

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/dir/MyClass.php',
            $this->tempDir.'/dir/dir/MyClass.php'
        );

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/dir/MySecondClass.php',
            $this->tempDir.'/dir/dir/MySecondClass.php'
        );

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/dir/MyThirdClass.php',
            $this->tempDir.'/dir/dir/MyThirdClass.php'
        );
    }

    public function testAddPrefixToDirectoryShouldIgnoreNotPHPFiles()
    {
        chdir($this->tempDir);

        $args = self::$command->parseArgs(new StringArgs('MyPrefix\\\\ dir'.DIRECTORY_SEPARATOR.'dir2'));

        $expected = <<<'EOF'

EOF;

        $this->assertSame(0, $this->handler->handle($args, $this->io));
        $this->assertSame($expected, $this->io->fetchOutput());
        $this->assertEmpty($this->io->fetchErrors());

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/dir2/NotAPHPFile.txt',
            $this->tempDir.'/dir/dir2/NotAPHPFile.txt'
        );
    }

    public function testAddPrefixToFile()
    {
        chdir($this->tempDir);

        $args = self::$command->parseArgs(
            new StringArgs('MyPrefix\\\\ dir'.DIRECTORY_SEPARATOR.'dir'.DIRECTORY_SEPARATOR.'MyClass.php')
        );

        $expected = <<<EOF
Scoping $this->tempDir/dir/dir/MyClass.php. . . Success

EOF;
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);

        $this->assertSame(0, $this->handler->handle($args, $this->io));
        $this->assertSame($expected, $this->io->fetchOutput());
        $this->assertEmpty($this->io->fetchErrors());

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/dir/MyClass.php',
            $this->tempDir.'/dir/dir/MyClass.php'
        );
    }

    public function testAddPrefixToMultiplePaths()
    {
        chdir($this->tempDir);

        $args = self::$command->parseArgs(
            new StringArgs(
                    'MyPrefix\\\\'.
                    ' dir'.DIRECTORY_SEPARATOR.'dir'.DIRECTORY_SEPARATOR.'MyClass.php'.
                    ' dir'.DIRECTORY_SEPARATOR.'dir'.DIRECTORY_SEPARATOR.'MySecondClass.php'
            )
        );

        $expected = <<<EOF
Scoping $this->tempDir/dir/dir/MyClass.php. . . Success
Scoping $this->tempDir/dir/dir/MySecondClass.php. . . Success

EOF;
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);

        $this->assertSame(0, $this->handler->handle($args, $this->io));
        $this->assertSame($expected, $this->io->fetchOutput());
        $this->assertEmpty($this->io->fetchErrors());

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/dir/MyClass.php',
            $this->tempDir.'/dir/dir/MyClass.php'
        );

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/dir/MySecondClass.php',
            $this->tempDir.'/dir/dir/MySecondClass.php'
        );
    }

    public function testAddPrefixToIncorrectFile()
    {
        chdir($this->tempDir);

        $args = self::$command->parseArgs(
            new StringArgs('MyPrefix\\\\ dir'.DIRECTORY_SEPARATOR.'MyIncorrectClass.php')
        );

        $expected = <<<EOF
Scoping $this->tempDir/dir/MyIncorrectClass.php. . . Fail

EOF;
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);

        $this->assertSame(0, $this->handler->handle($args, $this->io));
        $this->assertSame($expected, $this->io->fetchErrors());
    }
}
