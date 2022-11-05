<?php declare(strict_types=1);

namespace Acme;

require __DIR__.'/vendor/autoload.php';

use function error_reporting;
use function trigger_deprecation;
use const E_ALL;

trigger_deprecation('fixtures/set033', '1.0.0', 'Test');
