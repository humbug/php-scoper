<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\PhpScoper\Console\Command;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Console\Tests\ApplicationTest;
use Symfony\Component\Filesystem\Filesystem;
use function Webmozart\PhpScoper\makeTempDir;

/**
 * @covers \Webmozart\PhpScoper\Console\Command\AddPrefixCommand
 */
class AddPrefixCommandTest extends TestCase
{
    const FIXTURES_DIR = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'../fixtures';
    
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
    private $tempDir;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        if (file_exists($this->tempDir)) {
            chdir($this->tempDir);
        }

        if (null !== $this->appTester) {
            return;
        }

        $this->cwd = getcwd();

        $application = new Application('php-scoper-test');
        $application->addCommands([
            new AddPrefixCommand(),
        ]);
        $application->setAutoExit(false);

        $this->appTester = new ApplicationTester($application);


        $this->tempDir = makeTempDir('php-scoper', __CLASS__);

        $filesystem = new Filesystem();
        $filesystem->mirror(self::FIXTURES_DIR.DIRECTORY_SEPARATOR.'original', $this->tempDir);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        chdir($this->cwd);

        $filesystem = new Filesystem();
        $filesystem->remove($this->tempDir);
    }

    public function test_scope_all_files_found_in_a_directory()
    {
        $this->appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix',
                'paths' => [
                    $basePath = self::FIXTURES_DIR.DIRECTORY_SEPARATOR.'original'.DIRECTORY_SEPARATOR.'dir'.DIRECTORY_SEPARATOR.'dir'
                ],
            ],
            [
                'capture_stderr_separately' => true,
                'set'
            ]
        );

        $expected = <<<EOF
Scoping $basePath/MyClass.php. . . Success
Scoping $basePath/MySecondClass.php. . . Success
Scoping $basePath/MyThirdClass.php. . . Success

EOF;
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);

        Assert::assertEmpty(
            $this->appTester->getErrorOutput(true),
            $this->appTester->getErrorOutput(true)
        );
        Assert::assertSame(0, $this->appTester->getStatusCode());
        Assert::assertStringEndsWith($expected, $this->appTester->getDisplay(true));

        Assert::assertFileEquals(
            self::FIXTURES_DIR.'/replaced/dir/dir/MyClass.php',
            $this->tempDir.'/dir/dir/MyClass.php'
        );

        Assert::assertFileEquals(
            self::FIXTURES_DIR.'/replaced/dir/dir/MySecondClass.php',
            $this->tempDir.'/dir/dir/MySecondClass.php'
        );

        Assert::assertFileEquals(
            self::FIXTURES_DIR.'/replaced/dir/dir/MyThirdClass.php',
            $this->tempDir.'/dir/dir/MyThirdClass.php'
        );
    }

    public function test_does_not_scope_non_PHP_files()
    {
        $this->appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix\\\\',
                'paths' => ['dir'.DIRECTORY_SEPARATOR.'dir2'],
            ],
            ['capture_stderr_separately' => true]
        );

        $expected = <<<'EOF'
No PHP files to scope located with given path(s).

EOF;

        Assert::assertSame(0, $this->appTester->getStatusCode());
        Assert::assertStringEndsWith($expected, $this->appTester->getDisplay(true));
        Assert::assertEmpty($this->appTester->getErrorOutput(true));

        Assert::assertFileEquals(
            self::FIXTURES_DIR.'/replaced/dir/dir2/NotAPHPFile.txt',
            $this->tempDir.'/dir/dir2/NotAPHPFile.txt'
        );
    }

    public function test_can_scope_a_file()
    {
        $this->appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix\\\\',
                'paths' => ['dir'.DIRECTORY_SEPARATOR.'dir'.DIRECTORY_SEPARATOR.'MyClass.php'],
            ],
            ['capture_stderr_separately' => true]
        );

        $expected = <<<EOF
Scoping $this->tempDir/dir/dir/MyClass.php. . . Success

EOF;
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);

        Assert::assertSame(0, $this->appTester->getStatusCode());
        Assert::assertStringEndsWith($expected, $this->appTester->getDisplay(true));
        Assert::assertEmpty($this->appTester->getErrorOutput(true));

        Assert::assertFileEquals(
            self::FIXTURES_DIR.'/replaced/dir/dir/MyClass.php',
            $this->tempDir.'/dir/dir/MyClass.php'
        );
    }

    public function test_can_scope_for_multiple_paths()
    {
        $this->appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix\\\\',
                'paths' => [
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

        Assert::assertSame(0, $this->appTester->getStatusCode());
        Assert::assertStringEndsWith($expected, $this->appTester->getDisplay(true));
        Assert::assertEmpty($this->appTester->getErrorOutput(true));

        Assert::assertFileEquals(
            self::FIXTURES_DIR.'/replaced/dir/dir/MyClass.php',
            $this->tempDir.'/dir/dir/MyClass.php'
        );

        Assert::assertFileEquals(
            self::FIXTURES_DIR.'/replaced/dir/dir/MySecondClass.php',
            $this->tempDir.'/dir/dir/MySecondClass.php'
        );
    }

    public function test_ignore_invalid_files()
    {
        $this->appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix\\\\',
                'paths' => ['dir'.DIRECTORY_SEPARATOR.'MyIncorrectClass.php'],
            ],
            ['capture_stderr_separately' => true]
        );

        $expected = <<<EOF
Scoping $this->tempDir/dir/MyIncorrectClass.php. . . Fail

EOF;
        $expected = str_replace('/', DIRECTORY_SEPARATOR, $expected);

        Assert::assertSame(0, $this->appTester->getStatusCode());
        Assert::assertStringEndsWith($expected, $this->appTester->getDisplay(true));
    }

    public function test_cannot_scope_nonexistent_files()
    {
        $this->appTester->run(
            [
                'add-prefix',
                'prefix' => 'MyPrefix\\\\',
                'paths' => ['./the/path/to/nowhere'],
            ],
            ['capture_stderr_separately' => true]
        );

        Assert::assertSame(1, $this->appTester->getStatusCode());
    }
}
