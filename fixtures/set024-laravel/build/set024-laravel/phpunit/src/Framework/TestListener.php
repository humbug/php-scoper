<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Framework;

/**
 * A Listener for test progress.
 */
interface TestListener
{
    /**
     * An error occurred.
     */
    public function addError(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void;
    /**
     * A warning occurred.
     */
    public function addWarning(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Warning $e, float $time) : void;
    /**
     * A failure occurred.
     */
    public function addFailure(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\AssertionFailedError $e, float $time) : void;
    /**
     * Incomplete test.
     */
    public function addIncompleteTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void;
    /**
     * Risky test.
     */
    public function addRiskyTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void;
    /**
     * Skipped test.
     */
    public function addSkippedTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void;
    /**
     * A test suite started.
     */
    public function startTestSuite(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite $suite) : void;
    /**
     * A test suite ended.
     */
    public function endTestSuite(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite $suite) : void;
    /**
     * A test started.
     */
    public function startTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test) : void;
    /**
     * A test ended.
     */
    public function endTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, float $time) : void;
}
