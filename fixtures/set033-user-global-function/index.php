<?php declare(strict_types=1);

namespace Acme;

use function file_exists;

require file_exists(__DIR__.'/vendor/scoper-autoload.php')
    ? __DIR__.'/vendor/scoper-autoload.php'
    : __DIR__.'/vendor/autoload.php';

trigger_deprecation('fixtures/set033', '1.0.0', 'Test');
