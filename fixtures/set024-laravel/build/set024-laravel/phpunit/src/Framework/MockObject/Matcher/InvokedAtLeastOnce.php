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
/**
 * Invocation matcher which checks if a method has been invoked at least one
 * time.
 *
 * If the number of invocations is 0 it will throw an exception in verify.
 */
class InvokedAtLeastOnce extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedRecorder
{
    /**
     * @return string
     */
    public function toString() : string
    {
        return 'invoked at least once';
    }
    /**
     * Verifies that the current expectation is valid. If everything is OK the
     * code should just return, if not it must throw an exception.
     *
     * @throws ExpectationFailedException
     */
    public function verify() : void
    {
        $count = $this->getInvocationCount();
        if ($count < 1) {
            throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\ExpectationFailedException('Expected invocation at least once but it never occurred.');
        }
    }
}
