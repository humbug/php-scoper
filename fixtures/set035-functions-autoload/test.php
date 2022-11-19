<?php declare(strict_types=1);

$output = file_get_contents(__DIR__.'/output');

$result = preg_match(
    '#PHP Fatal error:  Uncaught Error: Call to undefined function GuzzleHttp\\describe_type\(\)#',
    $output,
);

exit(1 === $result ? 0 : 1);
