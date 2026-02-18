<?php declare(strict_types=1);

require file_exists(__DIR__.'/vendor/scoper-autoload.php')
    ? __DIR__.'/vendor/scoper-autoload.php'
    : __DIR__.'/vendor/autoload.php';

new Frame();
new Window();

echo "OK.".PHP_EOL;
