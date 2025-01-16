<?php

declare(strict_types=1);

require file_exists(__DIR__.'/vendor/scoper-autoload.php')
    ? __DIR__.'/vendor/scoper-autoload.php'
    : __DIR__.'/vendor/autoload.php';

echo foo() ? 'ok' : 'ko';
echo PHP_EOL;
echo bar() ? 'ok' : 'ko';
echo PHP_EOL;
