<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Assert\Assertion;
use Set005\Greeter;

Assertion::true(true);
echo (new Greeter())->greet().PHP_EOL;
