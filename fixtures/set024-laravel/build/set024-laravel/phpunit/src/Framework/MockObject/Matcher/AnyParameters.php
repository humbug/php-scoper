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
 * Invocation matcher which allows any parameters to a method.
 */
class AnyParameters extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Matcher\StatelessInvocation
{
    /**
     * @return string
     */
    public function toString() : string
    {
        return 'with any parameters';
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
