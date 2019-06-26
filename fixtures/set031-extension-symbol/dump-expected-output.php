<?php declare(strict_types=1);

$contents = str_replace(
    '__PATH_TO_PROJECT__',
    getcwd(),
    file_get_contents(__DIR__.'/expected-output.dist')
);

file_put_contents(__DIR__.'/expected-output', $contents);
