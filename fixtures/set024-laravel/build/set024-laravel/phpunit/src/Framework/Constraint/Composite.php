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
abstract class Composite extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint
{
    /**
     * @var Constraint
     */
    private $innerConstraint;
    public function __construct(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint $innerConstraint)
    {
        parent::__construct();
        $this->innerConstraint = $innerConstraint;
    }
    /**
     * Evaluates the constraint for parameter $other
     *
     * If $returnResult is set to false (the default), an exception is thrown
     * in case of a failure. null is returned otherwise.
     *
     * If $returnResult is true, the result of the evaluation is returned as
     * a boolean value instead: true in case of success, false in case of a
     * failure.
     *
     * @param mixed  $other        value or object to evaluate
     * @param string $description  Additional information about the test
     * @param bool   $returnResult Whether to return a result or throw an exception
     *
     * @throws ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return mixed
     */
    public function evaluate($other, $description = '', $returnResult = \false)
    {
        try {
            return $this->innerConstraint->evaluate($other, $description, $returnResult);
        } catch (\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException $e) {
            $this->fail($other, $description, $e->getComparisonFailure());
        }
    }
    /**
     * Counts the number of constraint elements.
     */
    public function count() : int
    {
        return \count($this->innerConstraint);
    }
    protected function innerConstraint() : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Constraint\Constraint
    {
        return $this->innerConstraint;
    }
}
