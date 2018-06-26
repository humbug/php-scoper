<?php

namespace _PhpScoper5b2c11ee6df50;

if ($neverHappens) {
    // @codeCoverageIgnoreStart
    print '*';
    // @codeCoverageIgnoreEnd
}
/**
 * @codeCoverageIgnore
 */
class Foo
{
    public function bar()
    {
    }
}
class Bar
{
    /**
     * @codeCoverageIgnore
     */
    public function foo()
    {
    }
}
function baz()
{
    print '*';
    // @codeCoverageIgnore
}
interface Bor
{
    public function foo();
}
