<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Set011\DirectionaryLocator;
use Set011\Greeter;
use Set011\Dictionary;

$testDir = dirname(Phar::running(false)).'/../tests';

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
