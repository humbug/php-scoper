<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation as BaseInvocation;
/**
 * Invocation matcher which checks if a method was invoked at a certain index.
 *
 * If the expected index number does not match the current invocation index it
 * will not match which means it skips all method and parameter matching. Only
 * once the index is reached will the method and parameter start matching and
 * verifying.
 *
 * If the index is never reached it will throw an exception in index.
 */
class InvokedAtIndex implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\Invocation
{
    /**
     * @var int
     */
    private $sequenceIndex;
    /**
     * @var int
     */
    private $currentIndex = -1;
    /**
     * @param int $sequenceIndex
     */
    public function __construct($sequenceIndex)
    {
        $this->sequenceIndex = $sequenceIndex;
    }
    /**
     * @return string
     */
    public function toString() : string
    {
        return 'invoked at sequence index ' . $this->sequenceIndex;
    }
    /**
     * @param BaseInvocation $invocation
     *
     * @return bool
     */
    public function matches(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        $this->currentIndex++;
        return $this->currentIndex == $this->sequenceIndex;
    }
    /**
     * @param BaseInvocation $invocation
     */
    public function invoked(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation) : void
    {
    }
    /**
     * Verifies that the current expectation is valid. If everything is OK the
     * code should just return, if not it must throw an exception.
     *
     * @throws ExpectationFailedException
     */
    public function verify() : void
    {
        if ($this->currentIndex < $this->sequenceIndex) {
            throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException(\sprintf('The expected invocation at index %s was never reached.', $this->sequenceIndex));
        }
    }
}
