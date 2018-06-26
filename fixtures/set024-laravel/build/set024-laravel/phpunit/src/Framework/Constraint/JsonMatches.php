<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException;
use _PhpScoper5b2c11ee6df50\PHPUnit\Util\Json;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Comparator\ComparisonFailure;
/**
 * Asserts whether or not two JSON objects are equal.
 */
class JsonMatches extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint
{
    /**
     * @var string
     */
    private $value;
    public function __construct(string $value)
    {
        parent::__construct();
        $this->value = $value;
    }
    /**
     * Returns a string representation of the object.
     */
    public function toString() : string
    {
        return \sprintf('matches JSON string "%s"', $this->value);
    }
    /**
     * Evaluates the constraint for parameter $other. Returns true if the
     * constraint is met, false otherwise.
     *
     * This method can be overridden to implement the evaluation algorithm.
     *
     * @param mixed $other value or object to evaluate
     */
    protected function matches($other) : bool
    {
        [$error, $recodedOther] = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Json::canonicalize($other);
        if ($error) {
            return \false;
        }
        [$error, $recodedValue] = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Json::canonicalize($this->value);
        if ($error) {
            return \false;
        }
        return $recodedOther == $recodedValue;
    }
    /**
     * Throws an exception for the given compared value and test description
     *
     * @param mixed             $other             evaluated value or object
     * @param string            $description       Additional information about the test
     * @param ComparisonFailure $comparisonFailure
     *
     * @throws ExpectationFailedException
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    protected function fail($other, $description, \_PhpScoper5b2c11ee6df50\SebastianBergmann\Comparator\ComparisonFailure $comparisonFailure = null) : void
    {
        if ($comparisonFailure === null) {
            [$error] = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Json::canonicalize($other);
            if ($error) {
                parent::fail($other, $description);
                return;
            }
            [$error] = \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Json::canonicalize($this->value);
            if ($error) {
                parent::fail($other, $description);
                return;
            }
            $comparisonFailure = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\Comparator\ComparisonFailure(\json_decode($this->value), \json_decode($other), \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Json::prettify($this->value), \_PhpScoper5b2c11ee6df50\PHPUnit\Util\Json::prettify($other), \false, 'Failed asserting that two json values are equal.');
        }
        parent::fail($other, $description, $comparisonFailure);
    }
}
