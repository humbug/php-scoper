<?php

declare(strict_types=1);

$autoload = __DIR__.'/vendor/scoper-autoload.php';

if (false === file_exists($autoload)) {
    $autoload = __DIR__.'/vendor/autoload.php';
}

require_once $autoload;

$foaf = new EasyRdf\Graph('http://njh.me/foaf.rdf');
$count = $foaf->load();

echo $count.PHP_EOL;
