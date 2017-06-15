<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Set004\Greeter;

echo (new Greeter())->greet().PHP_EOL;
