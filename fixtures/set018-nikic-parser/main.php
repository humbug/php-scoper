<?php

declare(strict_types=1);

use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

require file_exists(__DIR__.'/vendor/scoper-autoload.php')
    ? __DIR__.'/vendor/scoper-autoload.php'
    : __DIR__.'/vendor/autoload.php';

$code = <<<'CODE'
<?php

function test(int|float $foo)
{
    var_dump($foo);
}
CODE;

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

$ast = $parser->parse($code);

$dumper = new NodeDumper;

echo $dumper->dump($ast) . "\n";
