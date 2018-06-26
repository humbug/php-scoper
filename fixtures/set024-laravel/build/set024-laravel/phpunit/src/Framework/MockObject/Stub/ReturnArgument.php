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
/**
 * Stubs a method by returning an argument that was passed to the mocked method.
 */
class ReturnArgument implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub
{
    /**
     * @var int
     */
    private $argumentIndex;
    public function __construct($argumentIndex)
    {
        $this->argumentIndex = $argumentIndex;
    }
    public function invoke(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        if (isset($invocation->getParameters()[$this->argumentIndex])) {
            return $invocation->getParameters()[$this->argumentIndex];
        }
    }
    public function toString() : string
    {
        return \sprintf('return argument #%d', $this->argumentIndex);
    }
}
