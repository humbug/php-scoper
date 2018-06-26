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
 * Stubs a method by returning a user-defined value.
 */
class ReturnStub implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub
{
    /**
     * @var mixed
     */
    private $value;
    public function __construct($value)
    {
        $this->value = $value;
    }
    public function invoke(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        return $this->value;
    }
    public function toString() : string
    {
        $exporter = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\Exporter\Exporter();
        return \sprintf('return user-specified value %s', $exporter->export($this->value));
    }
}
