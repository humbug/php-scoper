<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation\ObjectInvocation;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\RuntimeException;
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub;
/**
 * Stubs a method by returning the current object.
 */
class ReturnSelf implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub
{
    public function invoke(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        if (!$invocation instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation\ObjectInvocation) {
            throw new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\RuntimeException('The current object can only be returned when mocking an ' . 'object, not a static class.');
        }
        return $invocation->getObject();
    }
    public function toString() : string
    {
        return 'return the current object';
    }
}
