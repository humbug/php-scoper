<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Util;

use _PhpScoper5b2c11ee6df50\Composer\Autoload\ClassLoader;
use _PhpScoper5b2c11ee6df50\DeepCopy\DeepCopy;
use _PhpScoper5b2c11ee6df50\Doctrine\Instantiator\Instantiator;
use _PhpScoper5b2c11ee6df50\PHP_Token;
use _PhpScoper5b2c11ee6df50\phpDocumentor\Reflection\DocBlock;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Generator;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
use _PhpScoper5b2c11ee6df50\Prophecy\Prophet;
use ReflectionClass;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\CodeCoverage;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Comparator\Comparator;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Diff\Diff;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Environment\Runtime;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Exporter\Exporter;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\FileIterator\Facade as FileIteratorFacade;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\GlobalState\Snapshot;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Invoker\Invoker;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\RecursionContext\Context;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Timer\Timer;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Version;
use _PhpScoper5b2c11ee6df50\Text_Template;
/**
 * Utility class for blacklisting PHPUnit's own source code files.
 */
final class Blacklist
{
    /**
     * @var array
     */
    public static $blacklistedClassNames = [\_PhpScoper5b2c11ee6df50\SebastianBergmann\FileIterator\Facade::class => 1, \_PhpScoper5b2c11ee6df50\SebastianBergmann\Timer\Timer::class => 1, \_PhpScoper5b2c11ee6df50\PHP_Token::class => 1, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase::class => 2, '_PhpScoper5b2c11ee6df50\\PHPUnit\\DbUnit\\TestCase' => 2, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Generator::class => 1, \_PhpScoper5b2c11ee6df50\Text_Template::class => 1, '_PhpScoper5b2c11ee6df50\\Symfony\\Component\\Yaml\\Yaml' => 1, \_PhpScoper5b2c11ee6df50\SebastianBergmann\CodeCoverage\CodeCoverage::class => 1, \_PhpScoper5b2c11ee6df50\SebastianBergmann\Diff\Diff::class => 1, \_PhpScoper5b2c11ee6df50\SebastianBergmann\Environment\Runtime::class => 1, \_PhpScoper5b2c11ee6df50\SebastianBergmann\Comparator\Comparator::class => 1, \_PhpScoper5b2c11ee6df50\SebastianBergmann\Exporter\Exporter::class => 1, \_PhpScoper5b2c11ee6df50\SebastianBergmann\GlobalState\Snapshot::class => 1, \_PhpScoper5b2c11ee6df50\SebastianBergmann\Invoker\Invoker::class => 1, \_PhpScoper5b2c11ee6df50\SebastianBergmann\RecursionContext\Context::class => 1, \_PhpScoper5b2c11ee6df50\SebastianBergmann\Version::class => 1, \_PhpScoper5b2c11ee6df50\Composer\Autoload\ClassLoader::class => 1, \_PhpScoper5b2c11ee6df50\Doctrine\Instantiator\Instantiator::class => 1, \_PhpScoper5b2c11ee6df50\phpDocumentor\Reflection\DocBlock::class => 1, \_PhpScoper5b2c11ee6df50\Prophecy\Prophet::class => 1, \_PhpScoper5b2c11ee6df50\DeepCopy\DeepCopy::class => 1];
    /**
     * @var string[]
     */
    private static $directories;
    /**
     * @return string[]
     */
    public function getBlacklistedDirectories() : array
    {
        $this->initialize();
        return self::$directories;
    }
    public function isBlacklisted(string $file) : bool
    {
        if (\defined('PHPUNIT_TESTSUITE')) {
            return \false;
        }
        $this->initialize();
        foreach (self::$directories as $directory) {
            if (\strpos($file, $directory) === 0) {
                return \true;
            }
        }
        return \false;
    }
    private function initialize() : void
    {
        if (self::$directories === null) {
            self::$directories = [];
            foreach (self::$blacklistedClassNames as $className => $parent) {
                if (!\class_exists($className)) {
                    continue;
                }
                $reflector = new \ReflectionClass($className);
                $directory = $reflector->getFileName();
                for ($i = 0; $i < $parent; $i++) {
                    $directory = \dirname($directory);
                }
                self::$directories[] = $directory;
            }
            // Hide process isolation workaround on Windows.
            if (\DIRECTORY_SEPARATOR === '\\') {
                // tempnam() prefix is limited to first 3 chars.
                // @see https://php.net/manual/en/function.tempnam.php
                self::$directories[] = \sys_get_temp_dir() . '\\PHP';
            }
        }
    }
}
