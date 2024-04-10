<?php declare(strict_types=1);

namespace Set040;

final class Php20
{
    public static function new_php20_function(bool $echo = false): void
    {
        if ($echo) {
            echo "Called `new_php20_function()`.";
        }
    }

    private function __construct()
    {
    }
}