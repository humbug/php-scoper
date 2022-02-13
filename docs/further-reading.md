## Further Reading

- [Polyfills](#polyfills)


### Polyfills

At the moment there is no way to automatically handles polyfills. This is mainly
due to the nature of polyfills: the code is sometimes a bit... special and there
is also not only one way on how to approach it.

If all of what you have is Symfony polyfills however, the following should get
you covered:

```php
<?php declare(strict_types=1);

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
    // ...
    
    'exclude-namespaces' => [
        'Symfony\Polyfill'
    ],
    'exclude-constants' => [
        // Symfony global constants
        '/^SYMFONY\_[\p{L}_]+$/',
    ],
    'exclude-files' => [
        ...$polyfillsBootstraps,
        ...$polyfillsStubs,
    ],
];

```


<br />
<hr />

« [Configuration](configuration.md#configuration) • [Limitations](limitations.md#limitations) »
