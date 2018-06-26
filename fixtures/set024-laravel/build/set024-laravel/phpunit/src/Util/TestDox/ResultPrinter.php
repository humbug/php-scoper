<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Util\TestDox;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\AssertionFailedError;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestListener;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Warning;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\WarningTestCase;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\BaseTestRunner;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Printer;
/**
 * Base class for printers of TestDox documentation.
 */
abstract class ResultPrinter extends \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Printer implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestListener
{
    /**
     * @var NamePrettifier
     */
    protected $prettifier;
    /**
     * @var string
     */
    protected $testClass = '';
    /**
     * @var int
     */
    protected $testStatus;
    /**
     * @var array
     */
    protected $tests = [];
    /**
     * @var int
     */
    protected $successful = 0;
    /**
     * @var int
     */
    protected $warned = 0;
    /**
     * @var int
     */
    protected $failed = 0;
    /**
     * @var int
     */
    protected $risky = 0;
    /**
     * @var int
     */
    protected $skipped = 0;
    /**
     * @var int
     */
    protected $incomplete = 0;
    /**
     * @var null|string
     */
    protected $currentTestClassPrettified;
    /**
     * @var null|string
     */
    protected $currentTestMethodPrettified;
    /**
     * @var array
     */
    private $groups;
    /**
     * @var array
     */
    private $excludeGroups;
    /**
     * @param resource $out
     * @param array    $groups
     * @param array    $excludeGroups
     *
     * @throws \PHPUnit\Framework\Exception
     */
    public function __construct($out = null, array $groups = [], array $excludeGroups = [])
    {
        parent::__construct($out);
        $this->groups = $groups;
        $this->excludeGroups = $excludeGroups;
        $this->prettifier = new \_PhpScoper5b2c11ee6df50\PHPUnit\Util\TestDox\NamePrettifier();
        $this->startRun();
    }
    /**
     * Flush buffer and close output.
     */
    public function flush() : void
    {
        $this->doEndClass();
        $this->endRun();
        parent::flush();
    }
    /**
     * An error occurred.
     */
    public function addError(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        if (!$this->isOfInterest($test)) {
            return;
        }
        $this->testStatus = \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\BaseTestRunner::STATUS_ERROR;
        $this->failed++;
    }
    /**
     * A warning occurred.
     */
    public function addWarning(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Warning $e, float $time) : void
    {
        if (!$this->isOfInterest($test)) {
            return;
        }
        $this->testStatus = \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\BaseTestRunner::STATUS_WARNING;
        $this->warned++;
    }
    /**
     * A failure occurred.
     */
    public function addFailure(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\AssertionFailedError $e, float $time) : void
    {
        if (!$this->isOfInterest($test)) {
            return;
        }
        $this->testStatus = \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\BaseTestRunner::STATUS_FAILURE;
        $this->failed++;
    }
    /**
     * Incomplete test.
     */
    public function addIncompleteTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        if (!$this->isOfInterest($test)) {
            return;
        }
        $this->testStatus = \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\BaseTestRunner::STATUS_INCOMPLETE;
        $this->incomplete++;
    }
    /**
     * Risky test.
     */
    public function addRiskyTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        if (!$this->isOfInterest($test)) {
            return;
        }
        $this->testStatus = \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\BaseTestRunner::STATUS_RISKY;
        $this->risky++;
    }
    /**
     * Skipped test.
     */
    public function addSkippedTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        if (!$this->isOfInterest($test)) {
            return;
        }
        $this->testStatus = \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\BaseTestRunner::STATUS_SKIPPED;
        $this->skipped++;
    }
    /**
     * A testsuite started.
     */
    public function startTestSuite(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite $suite) : void
    {
    }
    /**
     * A testsuite ended.
     */
    public function endTestSuite(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite $suite) : void
    {
    }
    /**
     * A test started.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function startTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test) : void
    {
        if (!$this->isOfInterest($test)) {
            return;
        }
        $class = \get_class($test);
        if ($this->testClass !== $class) {
            if ($this->testClass !== '') {
                $this->doEndClass();
            }
            $classAnnotations = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Test::parseTestMethodAnnotations($class);
            if (isset($classAnnotations['class']['testdox'][0])) {
                $this->currentTestClassPrettified = $classAnnotations['class']['testdox'][0];
            } else {
                $this->currentTestClassPrettified = $this->prettifier->prettifyTestClass($class);
            }
            $this->startClass($class);
            $this->testClass = $class;
            $this->tests = [];
        }
        if ($test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase) {
            $annotations = $test->getAnnotations();
            if (isset($annotations['method']['testdox'][0])) {
                $this->currentTestMethodPrettified = $annotations['method']['testdox'][0];
            } else {
                $this->currentTestMethodPrettified = $this->prettifier->prettifyTestMethod($test->getName(\false));
            }
            if ($test->usesDataProvider()) {
                $this->currentTestMethodPrettified .= ' ' . $test->dataDescription();
            }
        }
        $this->testStatus = \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\BaseTestRunner::STATUS_PASSED;
    }
    /**
     * A test ended.
     */
    public function endTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, float $time) : void
    {
        if (!$this->isOfInterest($test)) {
            return;
        }
        $this->tests[] = [$this->currentTestMethodPrettified, $this->testStatus];
        $this->currentTestClassPrettified = null;
        $this->currentTestMethodPrettified = null;
    }
    protected function doEndClass() : void
    {
        foreach ($this->tests as $test) {
            $this->onTest($test[0], $test[1] === \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\BaseTestRunner::STATUS_PASSED);
        }
        $this->endClass($this->testClass);
    }
    /**
     * Handler for 'start run' event.
     */
    protected function startRun() : void
    {
    }
    /**
     * Handler for 'start class' event.
     */
    protected function startClass(string $name) : void
    {
    }
    /**
     * Handler for 'on test' event.
     *
     * @param mixed $name
     */
    protected function onTest($name, bool $success = \true) : void
    {
    }
    /**
     * Handler for 'end class' event.
     */
    protected function endClass(string $name) : void
    {
    }
    /**
     * Handler for 'end run' event.
     */
    protected function endRun() : void
    {
    }
    private function isOfInterest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test) : bool
    {
        if (!$test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase) {
            return \false;
        }
        if ($test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\WarningTestCase) {
            return \false;
        }
        if (!empty($this->groups)) {
            foreach ($test->getGroups() as $group) {
                if (\in_array($group, $this->groups)) {
                    return \true;
                }
            }
            return \false;
        }
        if (!empty($this->excludeGroups)) {
            foreach ($test->getGroups() as $group) {
                if (\in_array($group, $this->excludeGroups)) {
                    return \false;
                }
            }
            return \true;
        }
        return \true;
    }
}
