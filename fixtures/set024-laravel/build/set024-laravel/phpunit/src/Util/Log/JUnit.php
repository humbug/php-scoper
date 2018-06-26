<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Util\Log;

use DOMDocument;
use DOMElement;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\AssertionFailedError;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExceptionWrapper;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\SelfDescribing;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestFailure;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestListener;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Warning;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Filter;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Printer;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml;
use ReflectionClass;
use ReflectionException;
/**
 * A TestListener that generates a logfile of the test execution in XML markup.
 *
 * The XML markup used is the same as the one that is used by the JUnit Ant task.
 */
class JUnit extends \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Printer implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestListener
{
    /**
     * @var DOMDocument
     */
    protected $document;
    /**
     * @var DOMElement
     */
    protected $root;
    /**
     * @var bool
     */
    protected $reportUselessTests = \false;
    /**
     * @var bool
     */
    protected $writeDocument = \true;
    /**
     * @var DOMElement[]
     */
    protected $testSuites = [];
    /**
     * @var int[]
     */
    protected $testSuiteTests = [0];
    /**
     * @var int[]
     */
    protected $testSuiteAssertions = [0];
    /**
     * @var int[]
     */
    protected $testSuiteErrors = [0];
    /**
     * @var int[]
     */
    protected $testSuiteFailures = [0];
    /**
     * @var int[]
     */
    protected $testSuiteSkipped = [0];
    /**
     * @var int[]
     */
    protected $testSuiteTimes = [0];
    /**
     * @var int
     */
    protected $testSuiteLevel = 0;
    /**
     * @var DOMElement
     */
    protected $currentTestCase;
    /**
     * Constructor.
     *
     * @param mixed $out
     * @param bool  $reportUselessTests
     *
     * @throws \PHPUnit\Framework\Exception
     */
    public function __construct($out = null, bool $reportUselessTests = \false)
    {
        $this->document = new \DOMDocument('1.0', 'UTF-8');
        $this->document->formatOutput = \true;
        $this->root = $this->document->createElement('testsuites');
        $this->document->appendChild($this->root);
        parent::__construct($out);
        $this->reportUselessTests = $reportUselessTests;
    }
    /**
     * Flush buffer and close output.
     */
    public function flush() : void
    {
        if ($this->writeDocument === \true) {
            $this->write($this->getXML());
        }
        parent::flush();
    }
    /**
     * An error occurred.
     *
     * @throws \InvalidArgumentException
     */
    public function addError(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->doAddFault($test, $t, $time, 'error');
        $this->testSuiteErrors[$this->testSuiteLevel]++;
    }
    /**
     * A warning occurred.
     *
     * @throws \InvalidArgumentException
     */
    public function addWarning(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Warning $e, float $time) : void
    {
        $this->doAddFault($test, $e, $time, 'warning');
        $this->testSuiteFailures[$this->testSuiteLevel]++;
    }
    /**
     * A failure occurred.
     *
     * @throws \InvalidArgumentException
     */
    public function addFailure(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\AssertionFailedError $e, float $time) : void
    {
        $this->doAddFault($test, $e, $time, 'failure');
        $this->testSuiteFailures[$this->testSuiteLevel]++;
    }
    /**
     * Incomplete test.
     */
    public function addIncompleteTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->doAddSkipped($test);
    }
    /**
     * Risky test.
     */
    public function addRiskyTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        if (!$this->reportUselessTests || $this->currentTestCase === null) {
            return;
        }
        $error = $this->document->createElement('error', \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::prepareString("Risky Test\n" . \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Filter::getFilteredStacktrace($t)));
        $error->setAttribute('type', \get_class($t));
        $this->currentTestCase->appendChild($error);
        $this->testSuiteErrors[$this->testSuiteLevel]++;
    }
    /**
     * Skipped test.
     */
    public function addSkippedTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->doAddSkipped($test);
    }
    /**
     * A testsuite started.
     */
    public function startTestSuite(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite $suite) : void
    {
        $testSuite = $this->document->createElement('testsuite');
        $testSuite->setAttribute('name', $suite->getName());
        if (\class_exists($suite->getName(), \false)) {
            try {
                $class = new \ReflectionClass($suite->getName());
                $testSuite->setAttribute('file', $class->getFileName());
            } catch (\ReflectionException $e) {
            }
        }
        if ($this->testSuiteLevel > 0) {
            $this->testSuites[$this->testSuiteLevel]->appendChild($testSuite);
        } else {
            $this->root->appendChild($testSuite);
        }
        $this->testSuiteLevel++;
        $this->testSuites[$this->testSuiteLevel] = $testSuite;
        $this->testSuiteTests[$this->testSuiteLevel] = 0;
        $this->testSuiteAssertions[$this->testSuiteLevel] = 0;
        $this->testSuiteErrors[$this->testSuiteLevel] = 0;
        $this->testSuiteFailures[$this->testSuiteLevel] = 0;
        $this->testSuiteSkipped[$this->testSuiteLevel] = 0;
        $this->testSuiteTimes[$this->testSuiteLevel] = 0;
    }
    /**
     * A testsuite ended.
     */
    public function endTestSuite(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestSuite $suite) : void
    {
        $this->testSuites[$this->testSuiteLevel]->setAttribute('tests', $this->testSuiteTests[$this->testSuiteLevel]);
        $this->testSuites[$this->testSuiteLevel]->setAttribute('assertions', $this->testSuiteAssertions[$this->testSuiteLevel]);
        $this->testSuites[$this->testSuiteLevel]->setAttribute('errors', $this->testSuiteErrors[$this->testSuiteLevel]);
        $this->testSuites[$this->testSuiteLevel]->setAttribute('failures', $this->testSuiteFailures[$this->testSuiteLevel]);
        $this->testSuites[$this->testSuiteLevel]->setAttribute('skipped', $this->testSuiteSkipped[$this->testSuiteLevel]);
        $this->testSuites[$this->testSuiteLevel]->setAttribute('time', \sprintf('%F', $this->testSuiteTimes[$this->testSuiteLevel]));
        if ($this->testSuiteLevel > 1) {
            $this->testSuiteTests[$this->testSuiteLevel - 1] += $this->testSuiteTests[$this->testSuiteLevel];
            $this->testSuiteAssertions[$this->testSuiteLevel - 1] += $this->testSuiteAssertions[$this->testSuiteLevel];
            $this->testSuiteErrors[$this->testSuiteLevel - 1] += $this->testSuiteErrors[$this->testSuiteLevel];
            $this->testSuiteFailures[$this->testSuiteLevel - 1] += $this->testSuiteFailures[$this->testSuiteLevel];
            $this->testSuiteSkipped[$this->testSuiteLevel - 1] += $this->testSuiteSkipped[$this->testSuiteLevel];
            $this->testSuiteTimes[$this->testSuiteLevel - 1] += $this->testSuiteTimes[$this->testSuiteLevel];
        }
        $this->testSuiteLevel--;
    }
    /**
     * A test started.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function startTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test) : void
    {
        $testCase = $this->document->createElement('testcase');
        $testCase->setAttribute('name', $test->getName());
        if ($test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase) {
            $class = new \ReflectionClass($test);
            $methodName = $test->getName(!$test->usesDataProvider());
            if ($class->hasMethod($methodName)) {
                $method = $class->getMethod($methodName);
                $testCase->setAttribute('class', $class->getName());
                $testCase->setAttribute('classname', \str_replace('\\', '.', $class->getName()));
                $testCase->setAttribute('file', $class->getFileName());
                $testCase->setAttribute('line', $method->getStartLine());
            }
        }
        $this->currentTestCase = $testCase;
    }
    /**
     * A test ended.
     */
    public function endTest(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, float $time) : void
    {
        if ($test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase) {
            $numAssertions = $test->getNumAssertions();
            $this->testSuiteAssertions[$this->testSuiteLevel] += $numAssertions;
            $this->currentTestCase->setAttribute('assertions', $numAssertions);
        }
        $this->currentTestCase->setAttribute('time', \sprintf('%F', $time));
        $this->testSuites[$this->testSuiteLevel]->appendChild($this->currentTestCase);
        $this->testSuiteTests[$this->testSuiteLevel]++;
        $this->testSuiteTimes[$this->testSuiteLevel] += $time;
        if (\method_exists($test, 'hasOutput') && $test->hasOutput()) {
            $systemOut = $this->document->createElement('system-out', \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::prepareString($test->getActualOutput()));
            $this->currentTestCase->appendChild($systemOut);
        }
        $this->currentTestCase = null;
    }
    /**
     * Returns the XML as a string.
     */
    public function getXML() : string
    {
        return $this->document->saveXML();
    }
    /**
     * Enables or disables the writing of the document
     * in flush().
     *
     * This is a "hack" needed for the integration of
     * PHPUnit with Phing.
     *
     * @param mixed $flag
     */
    public function setWriteDocument($flag) : ?string
    {
        if (\is_bool($flag)) {
            $this->writeDocument = $flag;
        }
    }
    /**
     * Method which generalizes addError() and addFailure()
     *
     * @param mixed $type
     *
     * @throws \InvalidArgumentException
     */
    private function doAddFault(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test, \Throwable $t, float $time, $type) : void
    {
        if ($this->currentTestCase === null) {
            return;
        }
        if ($test instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\SelfDescribing) {
            $buffer = $test->toString() . "\n";
        } else {
            $buffer = '';
        }
        $buffer .= \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestFailure::exceptionToString($t) . "\n" . \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Filter::getFilteredStacktrace($t);
        $fault = $this->document->createElement($type, \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Xml::prepareString($buffer));
        if ($t instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExceptionWrapper) {
            $fault->setAttribute('type', $t->getClassName());
        } else {
            $fault->setAttribute('type', \get_class($t));
        }
        $this->currentTestCase->appendChild($fault);
    }
    private function doAddSkipped(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $test) : void
    {
        if ($this->currentTestCase === null) {
            return;
        }
        $skipped = $this->document->createElement('skipped');
        $this->currentTestCase->appendChild($skipped);
        $this->testSuiteSkipped[$this->testSuiteLevel]++;
    }
}
