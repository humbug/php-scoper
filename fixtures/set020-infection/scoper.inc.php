<?php

declare(strict_types=1);

use Isolated\Symfony\Component\Finder\Finder;

$polyfillsBootstraps = array_map(
    static fn (SplFileInfo $fileInfo) => $fileInfo->getPathname(),
    iterator_to_array(
        Finder::create()
            ->files()
            ->in(__DIR__ . '/vendor/symfony/polyfill-*')
            ->name('bootstrap*.php'),
        false,
    ),
);

$polyfillsStubs = array_map(
    static fn (SplFileInfo $fileInfo) => $fileInfo->getPathname(),
    iterator_to_array(
        Finder::create()
            ->files()
            ->in(__DIR__ . '/vendor/symfony/polyfill-*/Resources/stubs')
            ->name('*.php'),
        false,
    ),
);

return [
    'exclude-namespaces' => [
        'Symfony\Polyfill'
    ],
    'exclude-constants' => [
        // Symfony global constants
        // TODO: switch to the following regex once regexes are supported here
        // https://github.com/humbug/php-scoper/issues/634
        '/^SYMFONY\_[\p{L}_]+$/',
        'SYMFONY_GRAPHEME_CLUSTER_RX',
    ],
    'exclude-files' => [
        ...$polyfillsBootstraps,
        ...$polyfillsStubs,
    ],
];
