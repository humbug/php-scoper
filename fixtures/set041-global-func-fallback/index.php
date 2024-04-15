<?php declare(strict_types=1);

namespace App;

use Set041\N1\N1Class;
use Set041\N2\N2Class;

require file_exists(__DIR__.'/vendor/scoper-autoload.php')
    ? __DIR__.'/vendor/scoper-autoload.php'
    : __DIR__.'/vendor/autoload.php';

N1Class::create();
N2Class::create();

echo "OK.".PHP_EOL;
