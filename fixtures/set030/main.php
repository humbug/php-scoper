<?php

declare(strict_types=1);

$autoload = __DIR__.'/vendor/scoper-autoload.php';

if (false === file_exists($autoload)) {
    $autoload = __DIR__.'/vendor/autoload.php';
}

require_once $autoload;

echo foo() ? 'ok' : 'ko';
echo PHP_EOL;
echo bar() ? 'ok' : 'ko';
echo PHP_EOL;
