<?php

use Isolated\Symfony\Component\Finder\Finder;

return [
    'finders' => [
        (new Finder())
            ->files()
            ->in(__DIR__.DIRECTORY_SEPARATOR.'dir'),
    ],
];
