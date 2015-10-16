<?php

/*
 * This file is part of the webmozart/php-scoper package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\Cli\Tests;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessUtils;
use Webmozart\PathUtil\Path;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PuliBinTest extends PHPUnit_Framework_TestCase
{
    private static $php;

    private $rootDir;

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

        $this->rootDir = TestUtil::makeTempDir('php-scoper', __CLASS__);
        $this->phpScoper = Path::canonicalize(__DIR__.'/../bin/php-scoper');

        $filesystem = new Filesystem();
        $filesystem->mirror(__DIR__.'/Fixtures/dir', $this->rootDir);
    }

    protected function tearDown()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->rootDir);
    }

    public function testAddPrefixToDir()
    {
        $output = $this->runPhpScoper(['add-prefix', 'MyPrefix\\', $this->rootDir]);

        // Test $output
    }

    private function runPhpScoper(array $args)
    {
        $php = escapeshellcmd(self::$php);
        $phpScoper = ProcessUtils::escapeArgument($this->phpScoper);
        $args = array_map([ProcessUtils::class, 'escapeArgument'], $args);
        $process = new Process($php.' '.$phpScoper.' '.implode(' ', $args), $this->rootDir);
        $status = $process->run();
        $output = (string) $process->getOutput();

        if (0 !== $status) {
            // for debugging
            var_dump($process->getErrorOutput());
        }

        $this->assertSame(0, $status);

        return $output;
    }
}
