<?php declare(strict_types=1);

$output = file_get_contents(__DIR__.'/output');

$expectedOutput = <<<'EOF'
Autoload Scoped code.
Autoload code.
OK.

EOF;

if ($output !== $expectedOutput) {
    echo 'FAILED!'.PHP_EOL;
    echo '–––––––'.PHP_EOL;
    echo $output;
    echo PHP_EOL;

    exit(1);
}

exit(0);
