<?php

use Isolated\Symfony\Component\Finder\Finder;

return [
    'finders' => [
        (new Finder())
            ->files()
            ->in(__DIR__),
    ],
    'exclude-functions' => ['trigger_deprecation'],
];
