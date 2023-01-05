<?php

declare(strict_types=1);

if (file_exists(__DIR__.'/../vendor/scoper-autoload.php')) {
    require_once __DIR__.'/../vendor/scoper-autoload.php';
    require __DIR__.'/scoped-greet.phar';
    echo "It works!".PHP_EOL;
    exit(0);
} else {
    require_once __DIR__.'/../vendor/autoload.php';
}

use Set011\DirectionaryLocator;
use Set011\Greeter;
use Set011\Dictionary;

$dir = Phar::running(false);

if ('' === $dir) {
    // Running outside of a PHAR
    $dir = __DIR__.DIRECTORY_SEPARATOR.'bin';
}

$testDir = dirname($dir).'/../tests';

$dictionaries = DirectionaryLocator::locateDictionaries($testDir);

$words = array_reduce(
    $dictionaries,
    function (array $words, Dictionary $dictionary): array {
        $words = array_merge($words, $dictionary->provideWords());

        return $words;
    },
    []
);

$greeter = new Greeter($words);

foreach ($greeter->greet() as $greeting) {
    echo $greeting.PHP_EOL;
}
