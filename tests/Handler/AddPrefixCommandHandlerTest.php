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
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PhpScoper\PhpScoperApplication;
use Webmozart\PhpScoper\PhpScoperApplicationConfig;
use Webmozart\PhpScoper\Tests\TestUtil;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AddPrefixCommandHandlerTest extends TestCase
{
    /**
     * @var ApplicationTester
     */
    private static $appTester;

    /**
     * @var string
     */
    private $workingDirectory;

    /**
     * @var string
     */
    private $tempDir;

    public static function setUpBeforeClass()
    {
        $app = new PhpScoperApplication();
        PhpScoperApplicationConfig::configure($app);
        $app->setAutoExit(false);
        self::$appTester = new ApplicationTester($app);
    }

    protected function setUp()
    {
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

        self::$appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix\\\\',
                'path' => ['dir'.DIRECTORY_SEPARATOR.'dir'],
            ],
            ['capture_stderr_separately' => true]
        );

        $expected = <<<EOF
Scoping $this->tempDir/dir/dir/MyClass.php. . . Success
Scoping $this->tempDir/dir/dir/MySecondClass.php. . . Success
Scoping $this->tempDir/dir/dir/MyThirdClass.php. . . Success

EOF;
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);

        $this->assertSame(0, self::$appTester->getStatusCode());
        $this->assertSame($expected, self::$appTester->getDisplay(true));
        $this->assertEmpty(self::$appTester->getErrorOutput(true));

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

        self::$appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix\\\\',
                'path' => ['dir'.DIRECTORY_SEPARATOR.'dir2'],
            ],
            ['capture_stderr_separately' => true]
        );

        $expected = <<<'EOF'

EOF;

        $this->assertSame(0, self::$appTester->getStatusCode());
        $this->assertSame($expected, self::$appTester->getDisplay(true));
        $this->assertEmpty(self::$appTester->getErrorOutput(true));

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/dir2/NotAPHPFile.txt',
            $this->tempDir.'/dir/dir2/NotAPHPFile.txt'
        );
    }

    public function testAddPrefixToFile()
    {
        chdir($this->tempDir);

        self::$appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix\\\\',
                'path' => ['dir'.DIRECTORY_SEPARATOR.'dir'.DIRECTORY_SEPARATOR.'MyClass.php'],
            ],
            ['capture_stderr_separately' => true]
        );

        $expected = <<<EOF
Scoping $this->tempDir/dir/dir/MyClass.php. . . Success

EOF;
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);

        $this->assertSame(0, self::$appTester->getStatusCode());
        $this->assertSame($expected, self::$appTester->getDisplay(true));
        $this->assertEmpty(self::$appTester->getErrorOutput(true));

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/dir/MyClass.php',
            $this->tempDir.'/dir/dir/MyClass.php'
        );
    }

    public function testAddPrefixToMultiplePaths()
    {
        chdir($this->tempDir);

        self::$appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix\\\\',
                'path' => [
                    'dir'.DIRECTORY_SEPARATOR.'dir'.DIRECTORY_SEPARATOR.'MyClass.php',
                    'dir'.DIRECTORY_SEPARATOR.'dir'.DIRECTORY_SEPARATOR.'MySecondClass.php',
                ],
            ],
            ['capture_stderr_separately' => true]
        );

        $expected = <<<EOF
Scoping $this->tempDir/dir/dir/MyClass.php. . . Success
Scoping $this->tempDir/dir/dir/MySecondClass.php. . . Success

EOF;
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);

        $this->assertSame(0, self::$appTester->getStatusCode());
        $this->assertSame($expected, self::$appTester->getDisplay(true));
        $this->assertEmpty(self::$appTester->getErrorOutput(true));

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

        self::$appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix\\\\',
                'path' => ['dir'.DIRECTORY_SEPARATOR.'MyIncorrectClass.php'],
            ],
            ['capture_stderr_separately' => true]
        );

        $expected = <<<EOF
Scoping $this->tempDir/dir/MyIncorrectClass.php. . . Fail

EOF;
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);

        $this->assertSame(0, self::$appTester->getStatusCode());
        $this->assertSame($expected, self::$appTester->getDisplay(true));
    }
}
