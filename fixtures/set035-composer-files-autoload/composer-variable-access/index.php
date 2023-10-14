<?php declare(strict_types=1);

require file_exists(__DIR__.'/vendor/scoper-autoload.php')
    ? __DIR__.'/vendor/scoper-autoload.php'
    : __DIR__.'/vendor/autoload.php';

if (!isset($GLOBALS['__composer_autoload_files'])) {
    // This is to mimic a scoped app that may access to the Composer related globals like
    // PHPStan does.
    throw new Exception('Expected to be able to access the composer autoload files!');
}
