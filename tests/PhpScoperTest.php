<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\PhpScoper\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;
use Webmozart\PathUtil\Path;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PhpScoperTest extends TestCase
{
    private static $php;

    private $tempDir;

    private $phpScoper;

    public static function setUpBeforeClass()
    {
        $phpFinder = new PhpExecutableFinder();

        self::$php = $phpFinder->find();
    }

    protected function setUp()
    {
        if (!self::$php) {
            $this->markTestSkipped('The "php" command could not be found.');
        }

        $this->tempDir = TestUtil::makeTempDir('php-scoper', __CLASS__);
        $this->phpScoper = Path::canonicalize(__DIR__.'/../bin/php-scoper');

        $filesystem = new Filesystem();
        $filesystem->mirror(__DIR__.'/Fixtures/original/dir', $this->tempDir);
    }

    protected function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->tempDir);
    }

    public function testAddPrefixToDir()
    {
        $output = $this->runPhpScoper(['add-prefix', 'MyPrefix\\', '-vvv', $this->tempDir]);

        /*
         * Just quickly check that directly running bin/php-scoper output
         * that it was working:
         */
        $this->assertStringStartsWith('Scoping', $output);
    }

    private function runPhpScoper(array $args)
    {
        $php = escapeshellcmd(self::$php);
        $phpScoper = ProcessUtils::escapeArgument($this->phpScoper);
        $args = array_map([ProcessUtils::class, 'escapeArgument'], $args);
        $process = new Process($php.' '.$phpScoper.' '.implode(' ', $args), $this->tempDir);
        $status = $process->run();
        $output = (string) $process->getOutput();

        if (0 !== $status) {
            // for debugging
            echo(PHP_EOL . $process->getErrorOutput() . PHP_EOL);
        }

        $this->assertSame(0, $status);

        return $output;
    }
}
