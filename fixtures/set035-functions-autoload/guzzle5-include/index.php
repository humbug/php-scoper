<?php declare(strict_types=1);

require file_exists(__DIR__.'/vendor/scoper-autoload.php')
    ? __DIR__.'/vendor/scoper-autoload.php'
    : __DIR__.'/vendor/autoload.php';

// Nothing to do: this file is purely here to autoload the scoped code.
// This should trigger the auto-loading of guzzlehttp/guzzle/src/functions_include.php
// which declares describe_type() – which is scoped.
