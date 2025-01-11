## Configuration

- [Prefix](#prefix)
- [PHP-Version](#php-version)
- [Output directory](#output-directory)
- [Finders and paths](#finders-and-paths)
- [Patchers](#patchers)
- [Excluded files](#excluded-files)
- [Excluded Symbols](#excluded-symbols)
- [Excluding namespaces](#excluding-namespaces)
- [Exposed Symbols](#exposed-symbols)
    - [Exposing namespaces](#exposing-namespaces)
    - [Exposing classes](#exposing-classes)
    - [Exposing functions](#exposing-functions)
    - [Exposing constants](#exposing-constants)

If you need more granular configuration, you can create a `scoper.inc.php` by
running the command `php-scoper init`. A different file/location can be passed
with a `--config` option.

Complete configuration reference (details about each entry is available):

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

The prefix to be used to isolate the code. If `null` or `''` (empty string) is given,
then a random prefix will be automatically generated.


### PHP Version

The PHP version provided is used to configure the underlying [PHP-Parser] Parser and Printer.

The version used by the Parser will affect what code it can understand, e.g. if it is configured in PHP 8.2 it will not
understand a PHP 8.3 construct (e.g. typed class constants). However, what symbols are interpreted as internal will
remain unchanged. The function `json_validate()` will be considered as internal even if the parser is configured with
PHP 8.2.

The printer version affects the code style. For example nowdocs and heredocs will be indented if the printer's PHP
version is higher than 7.4 but will be formated without indent otherwise.

If `null` or `''` (empty string) is given, then the host version will be used for the parser and 7.2 will be used for
the printer. This allows PHP-Scoper to a PHP 7.2 compatible codebase without breaking its compatibility although the
host version is a newer version.


### Output directory

The base output directory where the prefixed files will be generated. If `null`
is given, `build` is used.

This setting will be overridden by the command line option of the same name if
present.


### Finders and paths

By default, when running `php-scoper add-prefix`, it will prefix all relevant
code found in the current working directory. You can however define which
files should be scoped by using [Finders][symfony_finder] in the configuration:

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

Besides the finder, you can also add any path via the command:

```
php-scoper add-prefix file1.php bin/file2.php
```

Paths added manually are appended to the paths found by the finders.

If you are using [Box][box], all the (non-binary) files included are used
instead of the `finders` setting.


### Patchers

When scoping PHP files, there will be scenarios where some of the code being
scoped indirectly references the original namespace. These will include, for
example, strings or string manipulations. PHP-Scoper has limited support for
prefixing such strings, so you may need to define `patchers`, one or more
callables in a `scoper.inc.php` configuration file which can be used to replace
some of the code being scoped.

Here's a simple example:

* Class names in strings.

You can imagine instantiating a class from a variable which is based on a
known namespace, but also on a variable classname which is selected at
runtime. Perhaps code similar to:

```php
$type = 'Foo'; // determined at runtime
$class = 'Humbug\\Format\\Type\\' . $type;
```

If we scoped the `Humbug` namespace to `PhpScoperABC\Humbug`, then the above
snippet would fail as PHP-Scoper cannot interpret the above as being a namespaced
class. To complete the scoping successfully, a) the problem must
be located and b) the offending line replaced.

The patched code which would resolve this issue might be:

```php
$type = 'Foo'; // determined at runtime
$scopedPrefix = array_shift(explode('\\', __NAMESPACE__));
$class = $scopedPrefix . '\\Humbug\\Format\\Type\\' . $type;
```

This and similar issues *may* arise after scoping, and can be debugged by
running the scoped code and checking for issues. For this purpose, having a
couple of end-to-end tests to validate post-scoped code or PHARs is recommended.

Applying such a change can be achieved by defining a suitable patcher in
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

If you want to check if your patcher works as expected on a specific file, you can always check the scoping
result for a single file with the `inspect` command:

```shell
php-scoper inspect /path/to/offending/file
```


### Excluded files

For the files listed in `exclude-files`, their content will be left
untouched during the scoping process.


### Excluded Symbols

Symbols can be marked as excluded as follows:

```php
<?php declare(strict_types=1);

// scoper.inc.php

return [
    'exclude-namespaces' => [ 'WP', '/regex/' ],
    'exclude-classes' => ['Stringeable', '/regex/'],
    'exclude-functions' => ['str_contains', '/regex/'],
    'exclude-constants' => ['PHP_EOL', '/regex/'],
];
```

This enriches the list of Symbols PHP-Scoper's Reflector considers as "internal",
i.e. PHP engine or extension symbols. Such symbols will be left completely
untouched.*

*: There is _one_ exception, which is declarations of functions. If you have the function
`trigger_deprecation` excluded, then any usage of it in the code will be left alone:

```php
use function trigger_deprecation; // Will not be turned into Prefix\trigger_deprecation
```

However, PHP-Scoper may come across its declaration:

```php
// global namespace!

if (!function_exists('trigger_deprecation')) {
    function trigger_deprecation() {}
}
```

Then it will be scoped into:

```php
namespace Prefix;

if (!function_exists('Prefix\trigger_deprecation')) {
    function trigger_deprecation() {}
}
```

Indeed, the namespace _needs_ to be added in order to not break autoloading, in which
case wrapping the function declaration into a non-namespace could work, but is tricky
(so not implemented so far, PoC for supporting it are welcomed) hence was not attempted.

So if left alone, this will break any piece of code that relied on `\trigger_deprecation`,
which is why PHP-Scoper will still add an alias for it, as if it was an exposed function.
Another benefit of this, is that it allows to scope any polyfill without any issues.

**WARNING**: This exclusion feature should be use very carefully as it can easily break the Composer
auto-loading. Indeed, if you have the following package:

```json
{
    "autoload": {
        "psr-4": {
            "PHPUnit\\": "src"
        }
    }
}
```

And exclude the namespace `PHPUnit\Framework`, then the auto-loading for this
package will be faulty and will not work*. For this to work, the whole package
`PHPUnit` would need to be excluded.

*: With the regular Composer autoloader.

It is recommended to use excluded symbols only to complement the
[PhpStorm's stubs][phpstorm-stubs] shipped with PHP-Scoper.


### Excluding namespaces

When excluding a namespace by name, for example `'PHPUnit\Framework'`, any
symbol belonging to that namespace **or sub-namespace** will be excluded. For
example the class `'PHPUnit\Framework\TestCase\CommandTestCase'` would be
excluded as well.

As a result, registering the namespace name `''` will end up excluding any symbol.

To exclude symbols from the global namespace only, you should use a regex `/^$/`.
Indeed, regexes only exclude the matching namespaces.


### Exposed Symbols

PHP-Scoper's goal is to make sure that all code for a project lies in a
distinct PHP namespace. However, you may want to share a common API between
the bundled code of your PHAR and the consumer code. For example if you have
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
- An excluded symbol will not be exposed. If for example you expose the class
  `Acme\Foo` but the `Acme` namespace is excluded, then `Acme\Foo` will NOT
  be exposed.
- Exposing a namespace also exposes its sub-namespaces (with the aforementioned
  note applying)
- Exposing symbols will most likely require PHP-Scoper to adjust the Composer
  autoloader. To do so with minimal conflicts, PHP-Scoper dumps everything
  necessary in a `vendor/scoper-autoload.php` (which calls `vendor/autoload.php`).
  So do not forget to adjust your require statements for the scoped code to
  use this file instead. Note that this is automatically done by [Box][box] if
  you are using it with the [`PhpScoper` compactor][php-scoper-integration].

With this in mind, know that excluding a symbol may not be done the way you
expect it to. More details about the internal work, which will be necessary
if you need to delve into the scoped code, can be found bellow.

**Note: If a symbol is excluded _and_ exposed, the exclusion will take precedence.**

### Exposing Namespaces

The namespace configuration is identical to [excluding namespaces](#excluding-namespaces).

How the symbols are exposed is done as described in the next sections. Note
however that some symbols cannot be exposed (see [exposing/excluding traits](limitations.md#exposingexcluding-traits)
 and [exposing/excluding enums](limitations.md#exposingexcluding-enums))


### Exposing classes

In order to avoid any auto-loading issues, exposed classes are prefixed as usual
in the code-base but an alias pointing from the old symbol to the newly prefixed
one is registered.

So if you have the following file scoped with the class `Acme\Foo` exposed:

```php
<?php

namespace Acme;

class Foo {}
```

The prefixed code will look like something like this:

```php
<?php

namespace Humbug\Acme;

class Foo {}

\class_alias('Humbug\\Acme\\Foo', 'Acme\\Foo', \false);
```

And in `vendor/scoper-autoload.php` a `class_exist` statement is registered
to trigger the `class_alias` statement added:

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

The mechanism is very similar to the one used for classes. However since a
function similar to `class_alias` does not exists for functions, we declare
again the function with the right name.

So if you have the following file scoped with the function `dd` exposed:

```php
<?php

// No namespace: this is the global namespace

if (!function_exists('dd')) {
    function dd($args) {...}
}
```

The file will be scoped as usual:

```php
<?php

namespace PhpScoperPrefix;

if (!function_exists('PhpScoperPrefix\dd')) {
    function dd($args) {...}
}
```

And the following function which will serve as an alias will be
declared in the `scoper-autoload.php` file:


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

The constant aliasing mechanism is done by transforming the constant
declaration into a `define()` statement when this is not already the case.
Note that there is a difference here since `define()` defines a constant at
runtime whereas `const` defines it at compile time. You have a more details
post regarding the differences [here](https://stackoverflow.com/a/3193704/3902761)

Give the following file with the exposed constant `Acme\FOO`:

```php
<?php

namespace Acme;

const FOO = 'X';
```

The scoped file will look like this:

```php
<?php

namespace Humbug\Acme;

\define('FOO', 'X');
```


<br />
<hr />

« [Installation](installation.md#installation) • [Further Reading](further-reading.md#further-reading) »


[box]: https://github.com/box-project/box
[php-scoper-integration]: https://github.com/humbug/box#isolating-the-phar
[PHP-Parser]: https://github.com/nikic/PHP-Parser
[phpstorm-stubs]: https://github.com/JetBrains/phpstorm-stubs
[symfony_finder]: https://symfony.com/doc/current/components/finder.html
