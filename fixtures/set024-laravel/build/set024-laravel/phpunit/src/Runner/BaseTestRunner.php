<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Runner;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite;
use ReflectionClass;
use ReflectionException;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\FileIterator\Facade as FileIteratorFacade;
/**
 * Base class for all test runners.
 */
abstract class BaseTestRunner
{
    public const STATUS_UNKNOWN = -1;
    public const STATUS_PASSED = 0;
    public const STATUS_SKIPPED = 1;
    public const STATUS_INCOMPLETE = 2;
    public const STATUS_FAILURE = 3;
    public const STATUS_ERROR = 4;
    public const STATUS_RISKY = 5;
    public const STATUS_WARNING = 6;
    public const SUITE_METHODNAME = 'suite';
    /**
     * Returns the loader to be used.
     */
    public function getLoader() : \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestSuiteLoader
    {
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\StandardTestSuiteLoader();
    }
    /**
     * Returns the Test corresponding to the given suite.
     * This is a template method, subclasses override
     * the runFailed() and clearStatus() methods.
     *
     * @param string       $suiteClassName
     * @param string       $suiteClassFile
     * @param array|string $suffixes
     *
     * @throws Exception
     */
    public function getTest(string $suiteClassName, string $suiteClassFile = '', $suffixes = '') : ?\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test
    {
        if (\is_dir($suiteClassName) && !\is_file($suiteClassName . '.php') && empty($suiteClassFile)) {
            $facade = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\FileIterator\Facade();
            $files = $facade->getFilesAsArray($suiteClassName, $suffixes);
            $suite = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite($suiteClassName);
            $suite->addTestFiles($files);
            return $suite;
        }
        try {
            $testClass = $this->loadSuiteClass($suiteClassName, $suiteClassFile);
        } catch (\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception $e) {
            $this->runFailed($e->getMessage());
            return null;
        }
        try {
            $suiteMethod = $testClass->getMethod(self::SUITE_METHODNAME);
            if (!$suiteMethod->isStatic()) {
                $this->runFailed('suite() method must be static.');
                return null;
            }
            try {
                $test = $suiteMethod->invoke(null, $testClass->getName());
            } catch (\ReflectionException $e) {
                $this->runFailed(\sprintf("Failed to invoke suite() method.\n%s", $e->getMessage()));
                return null;
            }
        } catch (\ReflectionException $e) {
            try {
                $test = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite($testClass);
            } catch (\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception $e) {
                $test = new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite();
                $test->setName($suiteClassName);
            }
        }
        $this->clearStatus();
        return $test;
    }
    /**
     * Returns the loaded ReflectionClass for a suite name.
     */
    protected function loadSuiteClass(string $suiteClassName, string $suiteClassFile = '') : \ReflectionClass
    {
        $loader = $this->getLoader();
        return $loader->load($suiteClassName, $suiteClassFile);
    }
    /**
     * Clears the status message.
     */
    protected function clearStatus() : void
    {
    }
    /**
     * Override to define how to handle a failed loading of
     * a test suite.
     */
    protected abstract function runFailed(string $message);
}
