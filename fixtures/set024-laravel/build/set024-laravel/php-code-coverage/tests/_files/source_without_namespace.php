<?php

namespace _PhpScoper5b2c11ee6df50;

/**
 * Represents foo.
 */
class Foo
{
}
/**
 * @param mixed $bar
 */
function &foo($bar)
{
    $baz = function () {
    };
    $a = \true ? \true : \false;
    $b = "{$a}";
    $c = "{$b}";
}
