<?php

declare(strict_types=1);

namespace Set015;


use Pimple\Container;

class Greeter
{
    public function greet(Container $c): string
    {
        return $c['hello'];
    }
}
