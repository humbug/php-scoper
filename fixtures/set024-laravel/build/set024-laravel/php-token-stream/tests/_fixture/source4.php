<?php

namespace _PhpScoper5b2c11ee6df50;

// Declare the interface 'iTemplate'
interface iTemplate
{
    public function setVariable($name, $var);
    public function getHtml($template);
}
interface a
{
    public function foo();
}
interface b extends \_PhpScoper5b2c11ee6df50\a
{
    public function baz(\_PhpScoper5b2c11ee6df50\Baz $baz);
}
// short desc for class that implement a unique interface
class c implements \_PhpScoper5b2c11ee6df50\b
{
    public function foo()
    {
    }
    public function baz(\_PhpScoper5b2c11ee6df50\Baz $baz)
    {
    }
}
