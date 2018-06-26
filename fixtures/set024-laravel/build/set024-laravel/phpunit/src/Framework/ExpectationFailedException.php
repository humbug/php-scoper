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

use _PhpScoper5b2c11ee6df50\SebastianBergmann\Comparator\ComparisonFailure;
/**
 * Exception for expectations which failed their check.
 *
 * The exception contains the error message and optionally a
 * SebastianBergmann\Comparator\ComparisonFailure which is used to
 * generate diff output of the failed expectations.
 */
class ExpectationFailedException extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\AssertionFailedError
{
    /**
     * @var ComparisonFailure
     */
    protected $comparisonFailure;
    public function __construct(string $message, \_PhpScoper5b2c11ee6df50\SebastianBergmann\Comparator\ComparisonFailure $comparisonFailure = null, \Exception $previous = null)
    {
        $this->comparisonFailure = $comparisonFailure;
        parent::__construct($message, 0, $previous);
    }
    public function getComparisonFailure() : ?\_PhpScoper5b2c11ee6df50\SebastianBergmann\Comparator\ComparisonFailure
    {
        return $this->comparisonFailure;
    }
}
