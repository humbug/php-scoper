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
 * Stubs a method by returning a user-defined reference to a value.
 */
class ReturnReference implements \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Stub
{
    /**
     * @var mixed
     */
    private $reference;
    public function __construct(&$reference)
    {
        $this->reference =& $reference;
    }
    public function invoke(\_PhpScoper5b2c11ee6df50\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        return $this->reference;
    }
    public function toString() : string
    {
        $exporter = new \_PhpScoper5b2c11ee6df50\SebastianBergmann\Exporter\Exporter();
        return \sprintf('return user-specified reference %s', $exporter->export($this->reference));
    }
}
