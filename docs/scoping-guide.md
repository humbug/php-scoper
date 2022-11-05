## What symbol to scope how

When trying to scope a project, because of the dynamic nature of PHP, there is
a variety of combinations that cannot work.

Do be aware of the [known limitations] first.


### How to deal with polyfills

The current canonical way to deal with polyfills is as follows:

```php
<?php declare(strict_types=1);  // scoper.inc.php

use Isolated\Symfony\Component\Finder\Finder;

$polyfillsBootstraps = array_map(
    static fn (SplFileInfo $fileInfo) => $fileInfo->getPathname(),
    iterator_to_array(
        Finder::create()
            ->files()
            ->in(__DIR__.'/vendor/symfony/polyfill-*')
            ->name('bootstrap*.php'),
        false,
    ),
);

$polyfillsStubs = array_map(
    static fn (SplFileInfo $fileInfo) => $fileInfo->getPathname(),
    iterator_to_array(
        Finder::create()
            ->files()
            ->in(__DIR__.'/vendor/symfony/polyfill-*/Resources/stubs')
            ->name('*.php'),
        false,
    ),
);

return [
    'exclude-namespaces' => [
        'Symfony\Polyfill',
    ],
    'exclude-constants' => [
        // Symfony global constants
        '/^SYMFONY\_[\p{L}_]+$/',
    ],
    'exclude-files' => [
        ...$jetBrainStubs,
        ...$polyfillsBootstraps,
        ...$polyfillsStubs,
        ...$symfonyDeprecationContracts,
    ],
];
```


