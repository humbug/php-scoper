<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\SelfDescribing;
/**
 * An object that stubs the process of a normal method for a mock object.
 *
 * The stub object will replace the code for the stubbed method and return a
 * specific value instead of the original value.
 */
interface Stub extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\SelfDescribing
{
    /**
     * Fakes the processing of the invocation $invocation by returning a
     * specific value.
     *
     * @param Invocation $invocation The invocation which was mocked and matched by the current method and argument matchers
     *
     * @return mixed
     */
    public function invoke(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation);
}
