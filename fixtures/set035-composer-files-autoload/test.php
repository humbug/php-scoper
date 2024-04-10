<?php declare(strict_types=1);

$output = implode(
    "\n",
    array_filter(
        array_map(
            trim(...),
            explode(PHP_EOL, file_get_contents(__DIR__ . '/output')),
        ),
        static fn(string $line) => '' !== $line,
    ),
);

$expectedOutput = <<<'EOF'
Autoload Scoped code.
5.3.4.0
Guzzle5 loaded.
Autoload code.
The hashes are matching.
6.5.8.0
Done.
EOF;

if ($output !== $expectedOutput) {
    echo 'FAILED!' . PHP_EOL;
    echo 'Expected:' . PHP_EOL;
    echo '–––––––' . PHP_EOL;
    echo $expectedOutput . PHP_EOL;
    echo '–––––––' . PHP_EOL;
    echo 'Actual:' . PHP_EOL;
    echo '–––––––' . PHP_EOL;
    echo $output . PHP_EOL;
    echo '–––––––' . PHP_EOL;

    exit(1);
}

exit(0);
