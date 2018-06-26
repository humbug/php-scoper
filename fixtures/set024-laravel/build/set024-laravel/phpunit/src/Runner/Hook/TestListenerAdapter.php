<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Runner;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\AssertionFailedError;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestListener;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Warning;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Test as TestUtil;
final class TestListenerAdapter implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestListener
{
    /**
     * @var TestHook[]
     */
    private $hooks = [];
    /**
     * @var bool
     */
    private $lastTestWasNotSuccessful;
    public function add(\_PhpScoper5b2c11ee6df50\PHPUnit\Runner\TestHook $hook) : void
    {
        $this->hooks[] = $hook;
    }
    public function startTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\BeforeTestHook) {
                $hook->executeBeforeTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Util\Test::describeAsString($test));
            }
        }
        $this->lastTestWasNotSuccessful = \false;
    }
    public function addError(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\AfterTestErrorHook) {
                $hook->executeAfterTestError(\_PhpScoper5b2c11ee6df50\PHPUnit\Util\Test::describeAsString($test), $t->getMessage(), $time);
            }
        }
        $this->lastTestWasNotSuccessful = \true;
    }
    public function addWarning(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Warning $e, float $time) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\AfterTestWarningHook) {
                $hook->executeAfterTestWarning(\_PhpScoper5b2c11ee6df50\PHPUnit\Util\Test::describeAsString($test), $e->getMessage(), $time);
            }
        }
        $this->lastTestWasNotSuccessful = \true;
    }
    public function addFailure(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\AssertionFailedError $e, float $time) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\AfterTestFailureHook) {
                $hook->executeAfterTestFailure(\_PhpScoper5b2c11ee6df50\PHPUnit\Util\Test::describeAsString($test), $e->getMessage(), $time);
            }
        }
        $this->lastTestWasNotSuccessful = \true;
    }
    public function addIncompleteTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\AfterIncompleteTestHook) {
                $hook->executeAfterIncompleteTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Util\Test::describeAsString($test), $t->getMessage(), $time);
            }
        }
        $this->lastTestWasNotSuccessful = \true;
    }
    public function addRiskyTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\AfterRiskyTestHook) {
                $hook->executeAfterRiskyTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Util\Test::describeAsString($test), $t->getMessage(), $time);
            }
        }
        $this->lastTestWasNotSuccessful = \true;
    }
    public function addSkippedTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\AfterSkippedTestHook) {
                $hook->executeAfterSkippedTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Util\Test::describeAsString($test), $t->getMessage(), $time);
            }
        }
        $this->lastTestWasNotSuccessful = \true;
    }
    public function endTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, float $time) : void
    {
        if ($this->lastTestWasNotSuccessful === \true) {
            return;
        }
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\AfterSuccessfulTestHook) {
                $hook->executeAfterSuccessfulTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Util\Test::describeAsString($test), $time);
            }
        }
    }
    public function startTestSuite(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite $suite) : void
    {
    }
    public function endTestSuite(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite $suite) : void
    {
    }
}
