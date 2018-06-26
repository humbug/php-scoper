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

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation as BaseInvocation;
/**
 * Records invocations and provides convenience methods for checking them later
 * on.
 * This abstract class can be implemented by matchers which needs to check the
 * number of times an invocation has occurred.
 */
abstract class InvokedRecorder implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\Invocation
{
    /**
     * @var BaseInvocation[]
     */
    private $invocations = [];
    /**
     * @return int
     */
    public function getInvocationCount()
    {
        return \count($this->invocations);
    }
    /**
     * @return BaseInvocation[]
     */
    public function getInvocations()
    {
        return $this->invocations;
    }
    /**
     * @return bool
     */
    public function hasBeenInvoked()
    {
        return \count($this->invocations) > 0;
    }
    /**
     * @param BaseInvocation $invocation
     */
    public function invoked(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation) : void
    {
        $this->invocations[] = $invocation;
    }
    /**
     * @param BaseInvocation $invocation
     *
     * @return bool
     */
    public function matches(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        return \true;
    }
}
