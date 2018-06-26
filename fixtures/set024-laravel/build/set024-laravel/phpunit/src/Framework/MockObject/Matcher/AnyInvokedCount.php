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

/**
 * Invocation matcher which checks if a method has been invoked zero or more
 * times. This matcher will always match.
 */
class AnyInvokedCount extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\InvokedRecorder
{
    /**
     * @return string
     */
    public function toString() : string
    {
        return 'invoked zero or more times';
    }
    public function verify() : void
    {
    }
}
