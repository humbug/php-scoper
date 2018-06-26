<?php

namespace _PhpScoper5b2c11ee6df50;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
class NotExistingCoveredElementTest extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase
{
    /**
     * @covers NotExistingClass
     */
    public function testOne()
    {
    }
    /**
     * @covers NotExistingClass::notExistingMethod
     */
    public function testTwo()
    {
    }
    /**
     * @covers NotExistingClass::<public>
     */
    public function testThree()
    {
    }
}
