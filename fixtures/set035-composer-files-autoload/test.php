<?php declare(strict_types=1);

$output = file_get_contents(__DIR__.'/output');

$functionAutoloadFailed = 1 === preg_match(
    '#PHP Fatal error:  Uncaught Error: Call to undefined function GuzzleHttp\\describe_type\(\)#',
    $output,
);
$expectedResult = false;

if ($functionAutoloadFailed) {
    echo $output;
    echo PHP_EOL;
}

exit($functionAutoloadFailed === $expectedResult ? 0 : 1);
