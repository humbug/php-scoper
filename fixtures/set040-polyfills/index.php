<?php declare(strict_types=1);

namespace App;

use NewPhp20Interface;
use NewPhp20Class;
use function new_php20_function;
use const NEW_PHP20_CONSTANT;
use const PHP_EOL;

require file_exists(__DIR__.'/vendor/scoper-autoload.php')
    ? __DIR__.'/vendor/scoper-autoload.php'
    : __DIR__.'/vendor/autoload.php';

// This file mimics the execution of an app relying on a polyfill.
//
// From PHP-Scoper point of view, marking a symbol as excluded is
// akin to say this is a PHP native symbol.

// Consume the polyfilled code.

class NewPhp20Child implements NewPhp20Interface {}
new NewPhp20Class();
new_php20_function();
$x = NEW_PHP20_CONSTANT;

echo "OK.".PHP_EOL;
