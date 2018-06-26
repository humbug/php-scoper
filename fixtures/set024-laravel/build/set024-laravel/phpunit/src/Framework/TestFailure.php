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

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Error\Error;
use Throwable;
/**
 * A TestFailure collects a failed test together with the caught exception.
 */
class TestFailure
{
    /**
     * @var null|Test
     */
    protected $failedTest;
    /**
     * @var Throwable
     */
    protected $thrownException;
    /**
     * @var string
     */
    private $testName;
    /**
     * Returns a description for an exception.
     *
     * @param Throwable $e
     *
     * @throws \InvalidArgumentException
     */
    public static function exceptionToString(\Throwable $e) : string
    {
        if ($e instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\SelfDescribing) {
            $buffer = $e->toString();
            if ($e instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException && $e->getComparisonFailure()) {
                $buffer .= $e->getComparisonFailure()->getDiff();
            }
            if (!empty($buffer)) {
                $buffer = \trim($buffer) . "\n";
            }
            return $buffer;
        }
        if ($e instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Error\Error) {
            return $e->getMessage() . "\n";
        }
        if ($e instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExceptionWrapper) {
            return $e->getClassName() . ': ' . $e->getMessage() . "\n";
        }
        return \get_class($e) . ': ' . $e->getMessage() . "\n";
    }
    /**
     * Constructs a TestFailure with the given test and exception.
     *
     * @param Test      $failedTest
     * @param Throwable $t
     */
    public function __construct(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test $failedTest, $t)
    {
        if ($failedTest instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\SelfDescribing) {
            $this->testName = $failedTest->toString();
        } else {
            $this->testName = \get_class($failedTest);
        }
        if (!$failedTest instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase || !$failedTest->isInIsolation()) {
            $this->failedTest = $failedTest;
        }
        $this->thrownException = $t;
    }
    /**
     * Returns a short description of the failure.
     */
    public function toString() : string
    {
        return \sprintf('%s: %s', $this->testName, $this->thrownException->getMessage());
    }
    /**
     * Returns a description for the thrown exception.
     *
     * @throws \InvalidArgumentException
     */
    public function getExceptionAsString() : string
    {
        return self::exceptionToString($this->thrownException);
    }
    /**
     * Returns the name of the failing test (including data set, if any).
     */
    public function getTestName() : string
    {
        return $this->testName;
    }
    /**
     * Returns the failing test.
     *
     * Note: The test object is not set when the test is executed in process
     * isolation.
     *
     * @see Exception
     */
    public function failedTest() : ?\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Test
    {
        return $this->failedTest;
    }
    /**
     * Gets the thrown exception.
     */
    public function thrownException() : \Throwable
    {
        return $this->thrownException;
    }
    /**
     * Returns the exception's message.
     */
    public function exceptionMessage() : string
    {
        return $this->thrownException()->getMessage();
    }
    /**
     * Returns true if the thrown exception
     * is of type AssertionFailedError.
     */
    public function isFailure() : bool
    {
        return $this->thrownException() instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\AssertionFailedError;
    }
}
