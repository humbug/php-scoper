<?php

namespace _PhpScoper5b2c11ee6df50;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
class NamespaceCoverageMethodTest extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase
{
    /**
     * @covers Foo\CoveredClass::publicMethod
     */
    public function testSomething()
    {
        $o = new \_PhpScoper5b2c11ee6df50\Foo\CoveredClass();
        $o->publicMethod();
    }
}
