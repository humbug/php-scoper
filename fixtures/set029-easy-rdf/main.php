<?php

declare(strict_types=1);

$autoload = __DIR__.'/vendor/scoper-autoload.php';

if (false === file_exists($autoload)) {
    $autoload = __DIR__.'/vendor/autoload.php';
}

require_once $autoload;

$foaf = new EasyRdf_Graph();
$count = $foaf->parseFile(__DIR__.'/foaf.rdf');

echo $count.PHP_EOL;
