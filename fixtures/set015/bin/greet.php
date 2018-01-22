<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Set015\Greeter;
$c = new Pimple\Container(['hello' => 'Hello world!']);

echo (new Greeter())->greet($c).PHP_EOL;
