<?php

namespace _PhpScoper5b2c11ee6df50;

use _PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase;
/**
 * @coversDefaultClass \Foo\CoveredClass
 */
class NamespaceCoverageCoversClassPublicTest extends \_PhpScoper5b2c11ee6df50\PHPUnit\Framework\TestCase
{
    /**
     * @covers ::publicMethod
     */
    public function testSomething()
    {
        $o = new \_PhpScoper5b2c11ee6df50\Foo\CoveredClass();
        $o->publicMethod();
    }
}
