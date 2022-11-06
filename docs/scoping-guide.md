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


### How to deal with unknown third-party symbols

If you consider the following code:

```php
<?php

namespace Acme;

use function wp_list_users;

foreach (wp_list_users() as $user) {
    // ...
}
```

It would be scoped as follows:

```
<?php

namespace ScopingPrefix\Acme;

use function ScopingPrefix\wp_list_users;

foreach (wp_list_users() as $user) {
    // ...
}
```

This however will be a problem if your code (or your vendor) never declares
`wp_list_users`.

There is "two" ways to deal with this:

- excluding the symbol (recommended)
- exposing the symbol (fragile)

Excluding the symbol (see [excluded-symbols]) marks it as "internal", as if this
symbol was coming from PHP itself or a PHP extension. This is the most apt
solution 




[excluded-symbols]: configuration.md#excluded-symbols
[known limitations]: limitations.md
