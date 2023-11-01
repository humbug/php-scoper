<?php declare(strict_types=1);

namespace Acme;

use Composer\InstalledVersions;
use const PHP_EOL;

// Autoload the scoped vendor guzzle. This is to mimic a scoped code that would
// load this code.
// Triggering the autoloading will autoload the Guzzle `functions.php` file which
// declares the _scoped_ functions.
echo 'Autoload Scoped code.' . PHP_EOL;
require __DIR__ . '/scoped-guzzle5-include/index.php';

// Autoload the project autoload. This will trigger the autoloading of the files.
// Due to Composer creating a hash based on the package name & file path, the
// Guzzle file `functions.php` which contains the _non scoped_ facts but in fact
// will not be autoloaded since has the same hash.
echo 'Autoload code.' . PHP_EOL;
require __DIR__ . '/vendor/autoload.php';

// This is the test: it should have autoloaded the function file from the regular autoload
// despite the scoped file having been loaded previously.
\GuzzleHttp\describe_type('hello');

echo InstalledVersions::getVersion('guzzlehttp/guzzle') . PHP_EOL;
echo 'Done.' . PHP_EOL;
