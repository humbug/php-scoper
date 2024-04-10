<?php declare(strict_types=1);

use Set040\Php20;

if (PHP_VERSION_ID >= 200_000) {
    return;
}

if (!defined('NEW_PHP20_CONSTANT')) {
    define('NEW_PHP20_CONSTANT', 42);
}

if (!function_exists('new_php20_function')) {
    function new_php20_function(bool $echo = false): void { Php20::new_php20_function($echo); }
}
