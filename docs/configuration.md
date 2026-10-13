## Configuration

- [Prefix](#prefix)
- [PHP-Version](#php-version)
- [Output directory](#output-directory)
- [Finders and paths](#finders-and-paths)
- [Patchers](#patchers)
- [Excluded files](#excluded-files)
- [Symbols](#symbols)
- [Internal symbols](#internal-symbols)
    - [Internal classes](#internal-classes)
    - [Internal functions](#internal-functions)
    - [Internal constants](#internal-constants)
- [Excluded symbols](#excluded-symbols)
- [Excluding namespaces](#excluding-namespaces)
- [Exposed symbols](#exposed-symbols)
    - [Exposing namespaces](#exposing-namespaces)
    - [Exposing classes](#exposing-classes)
    - [Exposing functions](#exposing-functions)
    - [Exposing constants](#exposing-constants)
- [Symbols summary](#symbols-summary)

For more granular configuration, you can create a `scoper.inc.php` file by
running `php-scoper init`. A different file or location can be passed with the
`--config` option.

Complete configuration reference (each entry is detailed below):

```php
<?php declare(strict_types=1);

// scoper.inc.php

/** @var Symfony\Component\Finder\Finder $finder */
$finder = Isolated\Symfony\Component\Finder\Finder::class;

return [
    'prefix' => null,           // string|null
    'php-version' => null,      // string|null
    'output-dir' => null,       // string|null
    'finders' => [],            // list<Finder>
    'patchers' => [],           // list<callable(string $filePath, string $prefix, string $contents): string>

    'exclude-files' => [],      // list<string>
    'exclude-namespaces' => [], // list<string|regex>
    'exclude-constants' => [],  // list<string|regex>
    'exclude-classes' => [],    // list<string|regex>
    'exclude-functions' => [],  // list<string|regex>

    'expose-global-constants' => true,   // bool
    'expose-global-classes' => true,     // bool
    'expose-global-functions' => true,   // bool

    'expose-namespaces' => [], // list<string|regex>
    'expose-constants' => [],  // list<string|regex>
    'expose-classes' => [],    // list<string|regex>
    'expose-functions' => [],  // list<string|regex>
];
```


### Prefix

The prefix used to isolate the code. If `null` or `''` (empty string) is given,
a random prefix is generated automatically.


### PHP Version

The PHP version provided is used to configure the underlying [PHP-Parser] parser and printer.

The parser version determines which code it can understand: for example, a parser configured for PHP 8.2 will not
understand a PHP 8.3 construct such as typed class constants. However, which symbols are considered internal remains
unchanged: the function `json_validate()` is considered internal even if the parser is configured for PHP 8.2.

The printer version affects the code style. For example, nowdocs and heredocs are indented if the printer's PHP
version is higher than 7.4, and are formatted without indentation otherwise.

If `null` or `''` (empty string) is given, the host version is used for the parser and 7.2 for the printer. This
allows PHP-Scoper to scope a PHP 7.2-compatible codebase without breaking its compatibility, even when the host runs a
newer version.


### Output directory

The base output directory in which the prefixed files are generated. If `null`
is given, `build` is used.

This setting is overridden by the command-line option of the same name, if
present.


### Finders and paths

By default, `php-scoper add-prefix` prefixes all relevant code found in the
current working directory. You can, however, define which files should be
scoped by using [Finders][symfony_finder] in the configuration:

```php
<?php declare(strict_types=1);

// scoper.inc.php

/** @var Symfony\Component\Finder\Finder $finder */
$finder = Isolated\Symfony\Component\Finder\Finder::class;

return [
    'finders' => [
        $finder::create()->files()->in('src'),
        $finder::create()
            ->files()
            ->ignoreVCS(true)
            ->notName('/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/')
            ->exclude([
                'doc',
                'test',
                'test_old',
                'tests',
                'Tests',
                'vendor-bin',
            ])
            ->in('vendor'),
        $finder::create()->append([
            'bin/php-scoper',
            'composer.json',
        ])
    ],
];
```

In addition to the finders, you can pass any path directly to the command:

```
php-scoper add-prefix file1.php bin/file2.php
```

Paths added manually are appended to those found by the finders.

If you are using [Box][box], all the (non-binary) files it includes are used
instead of the `finders` setting.


### Patchers

When scoping PHP files, some of the code being scoped may reference the
original namespace indirectly, for example through strings or string
manipulation. PHP-Scoper has limited support for prefixing such strings, so you
may need to define `patchers`: one or more callables in the `scoper.inc.php`
configuration file that can be used to replace parts of the code being scoped.

Here is a simple example:

* Class names in strings.

Consider instantiating a class whose name is built from a known namespace and
a class name selected at runtime, for example:

```php
$type = 'Foo'; // determined at runtime
$class = 'Humbug\\Format\\Type\\' . $type;
```

If the `Humbug` namespace were scoped to `PhpScoperABC\Humbug`, the snippet
above would fail, as PHP-Scoper cannot interpret it as a namespaced class. To
complete the scoping successfully, a) the problem must be located and b) the
offending line replaced.

The patched code that resolves this issue could be:

```php
$type = 'Foo'; // determined at runtime
$scopedPrefix = explode('\\', __NAMESPACE__)[0];
$class = $scopedPrefix . '\\Humbug\\Format\\Type\\' . $type;
```

This and similar issues *may* arise after scoping and can be debugged by
running the scoped code and checking for errors. For this purpose, it is
recommended to have a few end-to-end tests that validate the scoped code or
PHARs.

Such a change can be applied by defining a suitable patcher in
`scoper.inc.php`:

```php
<?php declare(strict_types=1);

// scoper.inc.php

return [
    'patchers' => [
        static function (string $filePath, string $prefix, string $content): string {
            //
            // PHP-Parser patch conditions for file targets
            //
            if ($filePath === '/path/to/offending/file') {
                return preg_replace(
                    "%\$class = 'Humbug\\\\Format\\\\Type\\\\' . \$type;%",
                    '$class = \'' . $prefix . '\\\\Humbug\\\\Format\\\\Type\\\\\' . $type;',
                    $content
                );
            }

            return $content;
        },
    ],
];
```

To check whether your patcher works as expected on a specific file, you can inspect the scoping result for that
file with the `inspect` command:

```shell
php-scoper inspect /path/to/offending/file
```


### Excluded files

The contents of the files listed in `exclude-files` are left untouched during
scoping.


### Symbols

By default, every symbol (class, function or constant) is prefixed, both where
it is declared and where it is used. The exceptions are:

- [Internal symbols](#internal-symbols), i.e. the symbols provided by PHP
  itself or by a PHP extension, whose usages are left untouched.
- The symbols listed in `exclude-classes`, `exclude-functions` and
  `exclude-constants` (see [Excluded symbols](#excluded-symbols)), which are
  treated like internal symbols.
- The symbols belonging to an [excluded namespace](#excluding-namespaces),
  which are left entirely untouched, declarations included.
- [Exposed symbols](#exposed-symbols), which are prefixed but remain available
  under their original name.

Note that by default, the symbols of the global namespace are exposed (see the
`expose-global-constants`, `expose-global-classes` and `expose-global-functions`
settings in [Exposed symbols](#exposed-symbols)).

If a symbol is both excluded and exposed, the exclusion takes precedence. See
the [summary](#symbols-summary) for an overview of the possible cases.

To check how a given symbol is treated with your configuration, use the
`inspect-symbol` command:

```shell
php-scoper inspect-symbol PHP_EOL constant
```

```
…

Inspecting the symbol PHP_EOL for type constant:

 * Internal: true
 * Exposed:  true
```

The type (`class`, `function` or `constant`) is optional: by default, the
symbol is inspected as each of them. As with the other commands, the
configuration file is `scoper.inc.php` by default, and another one can be
passed with `--config`.

"Internal" covers both the internal symbols and the symbols listed in
`exclude-classes`, `exclude-functions` and `exclude-constants`. Excluded
namespaces, however, are not taken into account.


### Internal symbols

Internal symbols are the classes, functions and constants provided by PHP
itself or by a PHP extension, e.g. `strlen`, `Stringable` or `PHP_EOL`. Their
usages are never prefixed, as prefixing `strlen()` or `\Exception` would break
the code.

The list of internal symbols is static: it is made of the symbols known to the
[PhpStorm stubs][phpstorm-stubs], which cover all PHP versions and the most
common extensions, completed by PHP-Scoper for the symbols missing from the
stubs. In particular, it does not depend on the PHP version or the extensions
loaded when running PHP-Scoper, nor on the configured [PHP version](#php-version):
a symbol from an extension unknown to the stubs is prefixed, even if the
extension is loaded, e.g. `new \SomeExtensionClass()` becomes
`new \Humbug\SomeExtensionClass()`. You can check
whether a symbol is recognised as internal with the [`inspect-symbol`](#symbols)
command. If it is not, add it to the [excluded symbols](#excluded-symbols) (and
consider reporting it).

PHP-Scoper may also encounter the declaration of an internal symbol, typically
in a polyfill. As for any other file, the declaration is prefixed: the namespace
_needs_ to be added so as not to break autoloading. Wrapping the declaration in
a non-namespaced block could work, but is tricky and has therefore not been
implemented so far (proofs of concept supporting it are welcome). How
PHP-Scoper deals with it depends on the kind of symbol, as described in the
following sections.


### Internal classes

The usages of internal classes are left untouched.

If PHP-Scoper encounters the declaration of an internal class, for example:

```php
<?php

// No namespace: this is the global namespace

if (\PHP_VERSION_ID < 80000) {
    interface Stringable { public function __toString(): string; }
}
```

The declaration is prefixed. Since the usages of `Stringable` are left
untouched, PHP-Scoper also aliases `Humbug\Stringable` to `Stringable`, in the
same way as for [exposed classes](#exposing-classes):

```php
<?php

namespace Humbug;

if (\PHP_VERSION_ID < 80000) {
    interface Stringable
    {
        public function __toString(): string;
    }
    \class_alias('Humbug\Stringable', 'Stringable', \false);
}
```

This does not apply to traits and enums, which cannot be aliased (see
[exposing/excluding traits](limitations.md#exposingexcluding-traits) and
[exposing/excluding enums](limitations.md#exposingexcluding-enums)).


### Internal functions

The usages of internal functions are left untouched. Unqualified calls, e.g.
`strlen()` in a namespace, are not made fully qualified: PHP falls back to the
global function as before.

If PHP-Scoper encounters the declaration of an internal function, for example:

```php
<?php

// No namespace: this is the global namespace

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool { /* ... */ }
}
```

The declaration is prefixed, and the `function_exists()` check is adjusted to
check the prefixed function too:

```php
<?php

namespace Humbug;

if (!\function_exists('str_contains') && !\function_exists('Humbug\str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        /* ... */
    }
}
```

Since the usages of `str_contains` are left untouched, PHP-Scoper also declares
`str_contains` in `vendor/scoper-autoload.php`, in the same way as for
[exposed functions](#exposing-functions).


### Internal constants

PHP-Scoper treats internal constants as [exposed constants](#exposing-constants):

- Their usages are not prefixed. Since an unqualified constant could refer to a
  namespaced constant, PHP-Scoper makes them fully qualified, e.g. `PHP_EOL`
  becomes `\PHP_EOL`.
- Their declarations are transformed into `define()` statements using their
  original name, so no alias is needed. For example, `const E_USER_DEPRECATED = 16384;`
  becomes `\define('E_USER_DEPRECATED', 16384);`, and
  `define('E_USER_DEPRECATED', 16384);` keeps its name.

Unlike class and function names, constant names are case-sensitive: `PHP_EOL`
is internal but `php_eol` is not.


### Excluded symbols

Symbols can be marked as excluded as follows:

```php
<?php declare(strict_types=1);

// scoper.inc.php

return [
    'exclude-namespaces' => ['WP', '/regex/'],
    'exclude-classes' => ['WP_Error', '/regex/'],
    'exclude-functions' => ['trigger_deprecation', '/regex/'],
    'exclude-constants' => ['ABSPATH', '/regex/'],
];
```

`exclude-classes`, `exclude-functions` and `exclude-constants` extend the list
of [internal symbols](#internal-symbols): their usages are left untouched and
their declarations in the global namespace are aliased (classes and functions)
or declared with `define()` (constants), e.g. `trigger_deprecation` from
`symfony/deprecation-contracts`.

`exclude-namespaces` leaves untouched both the usages and the declarations of
the symbols belonging to the namespace (see [Excluding namespaces](#excluding-namespaces)).

No alias is added for excluded classes or functions declared outside the global
namespace: if `Acme\Foo` is excluded (but not the namespace `Acme`), its declaration
becomes `Humbug\Acme\Foo` whereas its usages remain `Acme\Foo`, which breaks the code.
Exclude the namespace instead.

**WARNING**: This exclusion feature should be used with great care, as it can easily break
Composer autoloading. For example, given the following package:

```json
{
    "autoload": {
        "psr-4": {
            "PHPUnit\\": "src"
        }
    }
}
```

If you exclude the namespace `PHPUnit\Framework`, autoloading for this package
will be broken*. For it to work, the whole `PHPUnit` package would need to be
excluded.

*: With the regular Composer autoloader.

It is recommended to use excluded symbols only to complement the
[PhpStorm stubs][phpstorm-stubs] shipped with PHP-Scoper.


### Excluding namespaces

When excluding a namespace by name, for example `'PHPUnit\Framework'`, any
symbol belonging to that namespace **or its sub-namespaces** is excluded. For
example, the class `'PHPUnit\Framework\TestCase\CommandTestCase'` would also be
excluded.

As a result, registering the namespace name `''` excludes every symbol.

To exclude symbols from the global namespace only, use the regex `/^$/`: regexes
exclude only the namespaces they match.


### Exposed symbols

PHP-Scoper's goal is to ensure that all of a project's code lies in a
distinct PHP namespace. However, you may want to share a common API between
the bundled code of your PHAR and the consumer code. For example, if you have
a PHPUnit PHAR with isolated code, you still want the PHAR to be able to
understand the `PHPUnit\Framework\TestCase` class.

Symbols can be marked as exposed as follows:

```php
<?php declare(strict_types=1);

// scoper.inc.php

return [
    'expose-global-constants' => false,
    'expose-global-classes' => false,
    'expose-global-functions' => false,

    'expose-namespaces' => ['PHPUnit\Framework', '/regex/'],
    'expose-classes' => ['PHPUnit\Configuration', '/regex/'],
    'expose-functions' => ['PHPUnit\execute_tests', '/regex/'],
    'expose-constants' => ['PHPUnit\VERSION', '/regex/'],
];
```

Notes:
- Exposing a namespace also exposes its sub-namespaces.
- Exposing symbols will most likely require PHP-Scoper to adjust the Composer
  autoloader. To do so with minimal conflicts, PHP-Scoper dumps everything
  necessary into `vendor/scoper-autoload.php` (which calls `vendor/autoload.php`).
  Remember to update the require statements of your scoped code to use this
  file instead. This is done automatically by [Box][box] if you use it with the
  [`PhpScoper` compactor][php-scoper-integration].

Bear in mind that a symbol may not be exposed in the way you expect. More
details about the internals, which you will need if you have to dig into the
scoped code, are given below.

If a symbol is both excluded and exposed, the exclusion takes precedence (see
[Symbols](#symbols)).

### Exposing namespaces

Namespaces are configured in the same way as when [excluding namespaces](#excluding-namespaces).

Symbols are exposed as described in the following sections. Note, however, that
some symbols cannot be exposed (see [exposing/excluding traits](limitations.md#exposingexcluding-traits)
and [exposing/excluding enums](limitations.md#exposingexcluding-enums)).


### Exposing classes

To avoid autoloading issues, exposed classes are prefixed as usual in the
codebase, but an alias from the original symbol to the newly prefixed one is
registered.

For example, if the following file is scoped with the class `Acme\Foo` exposed:

```php
<?php

namespace Acme;

class Foo {}
```

The prefixed code will look something like this:

```php
<?php

namespace Humbug\Acme;

class Foo {}

\class_alias('Humbug\\Acme\\Foo', 'Acme\\Foo', \false);
```

In `vendor/scoper-autoload.php`, a `class_exists` statement is registered to
trigger the added `class_alias` statement:

```php
<?php

// scoper-autoload.php @generated by PhpScoper

$loader = require_once __DIR__.'/autoload.php';

class_exists('Humbug\\Acme\\Foo');   // Triggers the auto-loading of
                                     // `Humbug\Acme\Foo` **AFTER** the
                                     // Composer autoload is registered

return $loader;
```


### Exposing functions

The mechanism is very similar to the one used for classes. However, since
there is no equivalent of `class_alias` for functions, the function is declared
again under the correct name.

For example, if the following file is scoped with the function `dd` exposed:

```php
<?php

// No namespace: this is the global namespace

if (!function_exists('dd')) {
    function dd($args) {...}
}
```

The file is scoped as usual:

```php
<?php

namespace PhpScoperPrefix;

if (!function_exists('PhpScoperPrefix\dd')) {
    function dd($args) {...}
}
```

The following function, which serves as an alias, is then declared in the
`scoper-autoload.php` file:

```php
<?php

// scoper-autoload.php @generated by PhpScoper

$loader = require_once __DIR__.'/autoload.php';

if (!function_exists('dd')) {
    function dd() {
        return \PhpScoperPrefix\dd(...func_get_args());
    }
}

return $loader;
```


### Exposing constants

Constants are aliased by transforming the constant declaration into a
`define()` statement, if it is not one already. Note that this introduces a
difference, since `define()` defines a constant at runtime whereas `const`
defines it at compile time. A more detailed explanation of the differences is
available [here](https://stackoverflow.com/a/3193704/3902761).

Given the following file with the exposed constant `Acme\FOO`:

```php
<?php

namespace Acme;

const FOO = 'X';
```

The scoped file will look like this:

```php
<?php

namespace Humbug\Acme;

\define('Acme\FOO', 'X');
```

Internal constants, including the ones listed in `exclude-constants`, are
treated as exposed constants (see [Internal constants](#internal-constants)).


### Symbols summary

The following table shows how a symbol is scoped depending on the
configuration, with the prefix `Humbug`. Functions behave like classes.

| Symbol                                     | Declaration                              | Usages            |
|--------------------------------------------|------------------------------------------|-------------------|
| `Acme\Foo`                                 | `Humbug\Acme\Foo`                        | `Humbug\Acme\Foo` |
| `Acme\Foo`, exposed                        | `Humbug\Acme\Foo`, aliased to `Acme\Foo` | `Humbug\Acme\Foo` |
| `Acme\Foo`, namespace `Acme` excluded      | `Acme\Foo`                               | `Acme\Foo`        |
| `Foo` (global namespace)**                 | `Humbug\Foo`, aliased to `Foo`           | `Humbug\Foo`      |
| `Foo` (global namespace), excluded         | `Humbug\Foo`, aliased to `Foo`           | `Foo`             |
| `Acme\Foo`, excluded*                      | `Humbug\Acme\Foo`                        | `Acme\Foo`        |
| `Acme\Foo`, excluded and exposed*          | `Humbug\Acme\Foo`                        | `Acme\Foo`        |
| `Acme\FOO` (constant), exposed or excluded | `\define('Acme\FOO', …)`                 | `Acme\FOO`        |
| Internal symbol, e.g. `strlen`             | (none), or as an excluded global symbol  | `strlen`          |

*: Broken, as the usages do not point to the declared symbol. See
[Excluded symbols](#excluded-symbols).

**: With `expose-global-classes` (or `expose-global-functions` for functions)
enabled, which is the default. Otherwise, it is not aliased.


<br />
<hr />

« [Installation](installation.md#installation) • [Further Reading](further-reading.md#further-reading) »


[box]: https://github.com/box-project/box
[php-scoper-integration]: https://github.com/humbug/box#isolating-the-phar
[PHP-Parser]: https://github.com/nikic/PHP-Parser
[phpstorm-stubs]: https://github.com/JetBrains/phpstorm-stubs
[symfony_finder]: https://symfony.com/doc/current/components/finder.html
