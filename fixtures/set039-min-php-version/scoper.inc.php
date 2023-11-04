<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

// You can do your own things here, e.g. collecting symbols to expose dynamically
// or files to exclude.
// However beware that this file is executed by PHP-Scoper, hence if you are using
// the PHAR it will be loaded by the PHAR. So it is highly recommended to avoid
// to auto-load any code here: it can result in a conflict or even corrupt
// the PHP-Scoper analysis.

// Example of collecting files to include in the scoped build but to not scope
// leveraging the isolated finder.
$excludedFiles = array_map(
    static fn (SplFileInfo $fileInfo) => $fileInfo->getPathName(),
    iterator_to_array(
        Finder::create()->files()->in(__DIR__),
        false,
    ),
);

return [
    'expose-classes' => ['App\Greeter'],
];
