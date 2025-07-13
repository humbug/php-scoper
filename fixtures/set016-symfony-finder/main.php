<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

require file_exists(__DIR__.'/vendor/scoper-autoload.php')
    ? __DIR__.'/vendor/scoper-autoload.php'
    : __DIR__.'/vendor/autoload.php';

$finder = Finder::create()->files()->in(__DIR__)->depth(0)->sortByName();

foreach ($finder as $fileInfo) {
    /** @var SplFileInfo $fileInfo */
    echo $fileInfo->getFilename().PHP_EOL;
}
