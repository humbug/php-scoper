<?php

namespace _PhpScoper5b2c11ee6df50;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
class CoverageProtectedTest extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase
{
    /**
     * @covers CoveredClass::<protected>
     */
    public function testSomething()
    {
        $o = new \_PhpScoper5b2c11ee6df50\CoveredClass();
        $o->publicMethod();
    }
}
