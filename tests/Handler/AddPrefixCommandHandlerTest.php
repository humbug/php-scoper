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
use Webmozart\PhpScoper\Console\Application;
use Webmozart\PhpScoper\Console\ApplicationConfig;
use Webmozart\PhpScoper\Tests\TestUtil;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AddPrefixCommandHandlerTest extends TestCase
{
    /**
     * @var ApplicationTester
     */
    private $appTester;

    /**
     * @var string
     */
    private $workingDirectory;

    /**
     * @var string
     */
    private $tempDir;

    protected function setUp()
    {
        if (is_null($this->appTester)) {
            $app = new Application();
            $conf = new ApplicationConfig();
            $conf->configure($app);
            $app->setAutoExit(false);
            $app->setCatchExceptions(false);
            $this->appTester = new ApplicationTester($app);
        }

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

        $this->appTester->run(
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

        $this->assertSame(0, $this->appTester->getStatusCode());
        $this->assertSame($expected, $this->appTester->getDisplay(true));
        $this->assertEmpty($this->appTester->getErrorOutput(true));

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

        $this->appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix\\\\',
                'path' => ['dir'.DIRECTORY_SEPARATOR.'dir2'],
            ],
            ['capture_stderr_separately' => true]
        );

        $expected = <<<'EOF'
No PHP files to scope located with given path(s).

EOF;

        $this->assertSame(0, $this->appTester->getStatusCode());
        $this->assertSame($expected, $this->appTester->getDisplay(true));
        $this->assertEmpty($this->appTester->getErrorOutput(true));

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/dir2/NotAPHPFile.txt',
            $this->tempDir.'/dir/dir2/NotAPHPFile.txt'
        );
    }

    public function testAddPrefixToFile()
    {
        chdir($this->tempDir);

        $this->appTester->run(
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

        $this->assertSame(0, $this->appTester->getStatusCode());
        $this->assertSame($expected, $this->appTester->getDisplay(true));
        $this->assertEmpty($this->appTester->getErrorOutput(true));

        $this->assertFileEquals(
            __DIR__.'/../Fixtures/replaced/dir/dir/MyClass.php',
            $this->tempDir.'/dir/dir/MyClass.php'
        );
    }

    public function testAddPrefixToMultiplePaths()
    {
        chdir($this->tempDir);

        $this->appTester->run(
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

        $this->assertSame(0, $this->appTester->getStatusCode());
        $this->assertSame($expected, $this->appTester->getDisplay(true));
        $this->assertEmpty($this->appTester->getErrorOutput(true));

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

        $this->appTester->run(
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

        $this->assertSame(0, $this->appTester->getStatusCode());
        $this->assertSame($expected, $this->appTester->getDisplay(true));
    }

    /**
     * @expectedException Webmozart\PhpScoper\Exception\RuntimeException
     */
    public function testNonExistingPathOrFileThrowsException()
    {
        chdir($this->tempDir);

        $this->appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix\\\\',
                'path' => ['./the/path/to/nowhere'],
            ],
            ['capture_stderr_separately' => true]
        );

        $this->assertSame(1, $this->appTester->getStatusCode());
    }
}
