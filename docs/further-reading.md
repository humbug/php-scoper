## Further Reading

- [Polyfills](#polyfills)
- [How to deal with unknown third-party symbols](#how-to-deal-with-unknown-third-party-symbols)
- [Autoload aliases](#autoload-aliases)
  - [Class aliases](#class-aliases)
  - [Function aliases](#function-aliases)


### Polyfills

**Note: should be obsolete as of 0.18.0.**

At the moment there is no way to automatically handles polyfills. This is mainly
due to the nature of polyfills: the code is sometimes a bit... special and there
is also not only one way on how to approach it.

If all of what you have is Symfony polyfills however, the following should get
you covered:

```php
<?php declare(strict_types=1);  // scoper.inc.php

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

**Excluding** the symbol (see [excluded-symbols]) marks it as "internal", as if this
symbol was coming from PHP itself or a PHP extension. This is the most appropriate
solution.

**Exposing** the symbol _may_ work but is more fragile. Indeed, exposing the
symbol will result in an alias being registered (see [exposed-symbols]), which
means you _need_ to have the function declared within your codebase at some point.


### Autoload aliases

#### Class aliases

When [exposing a class], an alias will be registered.

#### Function aliases

When [exposing a function] or when a globally declared [excluded function]
declaration is found (see [#706]), an alias will be registered.


<br />
<hr />

« [Configuration](configuration.md#configuration) • [Limitations](limitations.md#limitations) »

[excluded-symbols]: configuration.md#excluded-symbols
[excluding a function]: configuration.md#excluded-symbols
[exposed-symbols]: configuration.md#exposed-symbols
[exposing a class]: configuration.md#exposing-classes
[exposing a function]: configuration.md#exposing-functions
[#706]: https://github.com/humbug/php-scoper/pull/706
