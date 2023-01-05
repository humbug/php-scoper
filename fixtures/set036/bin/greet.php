<?php

declare(strict_types=1);

if (file_exists(__DIR__.'/../vendor/scoper-autoload.php')) {
    require_once __DIR__.'/../vendor/scoper-autoload.php';
} else {
    require_once __DIR__.'/../vendor/autoload.php';
}

use Set004\Greeter;

echo (new Greeter())->greet().PHP_EOL;
