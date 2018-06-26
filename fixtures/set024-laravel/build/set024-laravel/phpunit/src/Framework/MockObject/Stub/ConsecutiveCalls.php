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
use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub;
use _PhpScoper5b2c11ee6df50\SebastianBergmann\Exporter\Exporter;
/**
 * Stubs a method by returning a user-defined stack of values.
 */
class ConsecutiveCalls implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub
{
    /**
     * @var array
     */
    private $stack;
    /**
     * @var mixed
     */
    private $value;
    public function __construct(array $stack)
    {
        $this->stack = $stack;
    }
    public function invoke(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        $this->value = \array_shift($this->stack);
        if ($this->value instanceof \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub) {
            $this->value = $this->value->invoke($invocation);
        }
        return $this->value;
    }
    public function toString() : string
    {
        $exporter = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\Exporter\Exporter();
        return \sprintf('return user-specified value %s', $exporter->export($this->value));
    }
}
