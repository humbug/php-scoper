<?php

namespace _PhpScoper5b2c11ee6df50;

class Example
{
    public function even($numbers)
    {
        $numbers = \array_filter($numbers, function ($number) {
            return $number % 2 === 0;
        });
        return \array_merge($numbers);
    }
}
