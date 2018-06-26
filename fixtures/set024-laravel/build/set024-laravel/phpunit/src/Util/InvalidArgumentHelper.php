<?php

/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace _PhpScoper5b2c11ee6df50\PHPUnit\Util;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception;
/**
 * Factory for PHPUnit\Framework\Exception objects that are used to describe
 * invalid arguments passed to a function or method.
 */
final class InvalidArgumentHelper
{
    public static function factory(int $argument, string $type, $value = null) : \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception
    {
        $stack = \debug_backtrace();
        return new \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\Exception(\sprintf('Argument #%d%sof %s::%s() must be a %s', $argument, $value !== null ? ' (' . \gettype($value) . '#' . $value . ')' : ' (No Value) ', $stack[1]['class'], $stack[1]['function'], $type));
    }
}
