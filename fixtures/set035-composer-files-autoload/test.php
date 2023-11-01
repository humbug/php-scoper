<?php declare(strict_types=1);

$output = file_get_contents(__DIR__.'/output');

$expectedOutput = <<<'EOF'
Autoload Scoped code.
5.3.4.0
Guzzle5 loaded.Autoload code.
6.5.8.0
Done.

EOF;

if ($output !== $expectedOutput) {
    echo 'FAILED!' . PHP_EOL;
    echo '–––––––' . PHP_EOL;
    echo $output;
    echo PHP_EOL;

    exit(1);
}

exit(0);
