<?php

declare(strict_types=1);

require file_exists(__DIR__.'/vendor/scoper-autoload.php')
    ? __DIR__.'/vendor/scoper-autoload.php'
    : __DIR__.'/vendor/autoload.php';

$foaf = new EasyRdf\Graph('http://njh.me/foaf.rdf');
$count = $foaf->load();

echo $count.PHP_EOL;
