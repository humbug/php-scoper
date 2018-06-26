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
 * Stubs a method by raising a user-defined exception.
 */
class Exception implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub
{
    private $exception;
    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;
    }
    public function invoke(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation) : void
    {
        throw $this->exception;
    }
    public function toString() : string
    {
        $exporter = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\Exporter\Exporter();
        return \sprintf('raise user-specified exception %s', $exporter->export($this->exception));
    }
}
