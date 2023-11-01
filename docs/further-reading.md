## Further Reading

- [How to deal with unknown third-party symbols](#how-to-deal-with-unknown-third-party-symbols)
- [Autoload aliases](#autoload-aliases)
  - [Class aliases](#class-aliases)
  - [Function aliases](#function-aliases)


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
