<?php

use Closure;
$foo = new \Closure();
function foo(\Closure $bar)
{
}
$foo = new Closure();
function foo(Closure $bar)
{
}
