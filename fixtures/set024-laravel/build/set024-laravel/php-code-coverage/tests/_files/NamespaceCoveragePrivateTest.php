<?php

namespace _PhpScoper5b2c11ee6df50;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
class NamespaceCoveragePrivateTest extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase
{
    /**
     * @covers Foo\CoveredClass::<private>
     */
    public function testSomething()
    {
        $o = new \_PhpScoper5b2c11ee6df50\Foo\CoveredClass();
        $o->publicMethod();
    }
}
