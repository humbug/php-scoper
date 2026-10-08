<?php

declare(strict_types=1);

require file_exists(__DIR__.'/vendor/scoper-autoload.php')
    ? __DIR__.'/vendor/scoper-autoload.php'
    : __DIR__.'/vendor/autoload.php';

$foaf = new EasyRdf\Graph();
$count = $foaf->parseFile(__DIR__.'/foaf.rdf');

echo $count.PHP_EOL;
