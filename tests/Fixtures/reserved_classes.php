<?php

use Closure;
// FQNs
$foo = new \Closure();
function foo(\Closure $bar)
{
    $a = \PHP_EOL;
}
// No FQNs
$foo = new Closure();
function foo(Closure $bar)
{
    $a = PHP_EOL;
}
