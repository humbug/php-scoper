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
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestResult;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Warning;
use _PhpScoper5b2c11ee6df50\PHPUnit\Runner\PhptTestCase;
use _PhpScoper5b2c11ee6df50\PHPUnit\TextUI\ResultPrinter;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\TestDox\TestResult as TestDoxTestResult;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Timer\Timer;
/**
 * This printer is for CLI output only. For the classes that output to file, html and xml,
 * please refer to the PHPUnit\Util\TestDox namespace
 */
class CliTestDoxPrinter extends \_PhpScoper5b2c11ee6df50\PHPUnit\TextUI\ResultPrinter
{
    /**
     * @var TestDoxTestResult
     */
    private $currentTestResult;
    /**
     * @var TestDoxTestResult
     */
    private $previousTestResult;
    /**
     * @var TestDoxTestResult[]
     */
    private $nonSuccessfulTestResults = [];
    /**
     * @var NamePrettifier
     */
    private $prettifier;
    public function __construct($out = null, bool $verbose = \false, $colors = self::COLOR_DEFAULT, bool $debug = \false, $numberOfColumns = 80, bool $reverse = \false)
    {
        parent::__construct($out, $verbose, $colors, $debug, $numberOfColumns, $reverse);
        $this->prettifier = new \_PhpScoper5b2c11ee6df50\PHPUnit\Util\TestDox\NamePrettifier();
    }
    public function startTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test) : void
    {
        if (!$test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase && !$test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\PhptTestCase && !$test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite) {
            return;
        }
        $class = \get_class($test);
        if ($test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase) {
            $annotations = $test->getAnnotations();
            if (isset($annotations['class']['testdox'][0])) {
                $className = $annotations['class']['testdox'][0];
            } else {
                $className = $this->prettifier->prettifyTestClass($class);
            }
            if (isset($annotations['method']['testdox'][0])) {
                $testMethod = $annotations['method']['testdox'][0];
            } else {
                $testMethod = $this->prettifier->prettifyTestMethod($test->getName(\false));
            }
            $testMethod .= \substr($test->getDataSetAsString(\false), 5);
        } elseif ($test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite) {
            $className = $test->getName();
            $testMethod = \sprintf('Error bootstapping suite (most likely in %s::setUpBeforeClass)', $test->getName());
        } elseif ($test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\PhptTestCase) {
            $className = $class;
            $testMethod = $test->getName();
        }
        $this->currentTestResult = new \_PhpScoper5b2c11ee6df50\PHPUnit\Util\TestDox\TestResult(function (string $color, string $buffer) {
            return $this->formatWithColor($color, $buffer);
        }, $className, $testMethod);
        parent::startTest($test);
    }
    public function endTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, float $time) : void
    {
        if (!$test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase && !$test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Runner\PhptTestCase && !$test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite) {
            return;
        }
        parent::endTest($test, $time);
        $this->currentTestResult->setRuntime($time);
        $this->write($this->currentTestResult->toString($this->previousTestResult, $this->verbose));
        $this->previousTestResult = $this->currentTestResult;
        if (!$this->currentTestResult->isTestSuccessful()) {
            $this->nonSuccessfulTestResults[] = $this->currentTestResult;
        }
    }
    public function addError(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->currentTestResult->fail($this->formatWithColor('fg-yellow', '✘'), (string) $t);
    }
    public function addWarning(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Warning $e, float $time) : void
    {
        $this->currentTestResult->fail($this->formatWithColor('fg-yellow', '✘'), (string) $e);
    }
    public function addFailure(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\AssertionFailedError $e, float $time) : void
    {
        $this->currentTestResult->fail($this->formatWithColor('fg-red', '✘'), (string) $e);
    }
    public function addIncompleteTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->currentTestResult->fail($this->formatWithColor('fg-yellow', '∅'), (string) $t, \true);
    }
    public function addRiskyTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->currentTestResult->fail($this->formatWithColor('fg-yellow', '☢'), (string) $t, \true);
    }
    public function addSkippedTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->currentTestResult->fail($this->formatWithColor('fg-yellow', '→'), (string) $t, \true);
    }
    public function writeProgress(string $progress) : void
    {
    }
    public function flush() : void
    {
    }
    public function printResult(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestResult $result) : void
    {
        $this->printHeader();
        $this->printNonSuccessfulTestsSummary($result->count());
        $this->printFooter($result);
    }
    protected function printHeader() : void
    {
        $this->write("\n" . \_PhpScoper5b2c11ee6df50\SebastianBergmann\Timer\Timer::resourceUsage() . "\n\n");
    }
    private function printNonSuccessfulTestsSummary(int $numberOfExecutedTests) : void
    {
        $numberOfNonSuccessfulTests = \count($this->nonSuccessfulTestResults);
        if ($numberOfNonSuccessfulTests === 0) {
            return;
        }
        if ($numberOfNonSuccessfulTests / $numberOfExecutedTests >= 0.7) {
            return;
        }
        $this->write("Summary of non-successful tests:\n\n");
        $previousTestResult = null;
        foreach ($this->nonSuccessfulTestResults as $testResult) {
            $this->write($testResult->toString($previousTestResult, $this->verbose));
            $previousTestResult = $testResult;
        }
    }
}
