# PHP-Scoper

[![Package version](https://img.shields.io/packagist/v/humbug/php-scoper.svg?style=flat-square)](https://packagist.org/packages/humbug/php-scoper)
[![Travis Build Status](https://img.shields.io/travis/humbug/php-scoper.svg?branch=master&style=flat-square)](https://travis-ci.org/humbug/php-scoper?branch=master)
[![AppVeyor Build Status](https://img.shields.io/appveyor/ci/humbug/php-scoper.svg?branch=master&style=flat-square)](https://ci.appveyor.com/project/humbug/php-scoper/branch/master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/humbug/php-scoper.svg?branch=master&style=flat-square)](https://scrutinizer-ci.com/g/humbug/php-scoper/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/humbug/php-scoper/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/humbug/php-scoper/?branch=master)
[![Slack](https://img.shields.io/badge/slack-%23humbug-red.svg?style=flat-square)](https://symfony.com/slack-invite)
[![License](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](LICENSE)

PHP-Scoper is a tool which essentially moves any body of code, including all
dependencies such as vendor directories, to a new and distinct namespace.


## Goal

PHP-Scoper's goal is to make sure that all code for a project lies in a 
distinct PHP namespace. This is necessary, for example, when building PHARs that:

* Bundle their own vendor dependencies; and
* Load/execute code from arbitrary PHP projects with similar dependencies

When a package (of possibly different versions) exists, and is found in both a PHAR
and the executed code, the one from the PHAR will be used. This means these
PHARs run the risk of raising conflicts between their bundled vendors and the
vendors of the project they are interacting with, leading to issues that are 
potentially very difficult to debug due to dissimilar or unsupported package versions.


## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
    - [PHAR](#phar)
    - [Composer](#composer)
- [Configuration](#configuration)
    - [Prefix](#prefix)
    - [Finders and paths](#finders-and-paths)
    - [Patchers](#patchers)
    - [Whitelist][whitelist]
        - [Constants from the global namespace whitelisting](#constants-from-the-global-namespace-whitelisting)
        - [Classes & Constants whitelisting](#classes--constants-whitelisting)
        - [Global user functions](#global-user-functions)
        - [Namespaces whitelisting](#namespaces-whitelisting)
- [Building a scoped PHAR](#building-a-scoped-phar)
    - [With Box](#with-box)
        - [Step 1: Configure build location and prep vendors](#step-1-configure-build-location-and-prep-vendors)
        - [Step 2: Run PHP-Scoper](#step-2-run-php-scoper)
    - [Without Box](#without-box)
- [Recommendations](#recommendations)
- [Limitations](#limitations)
    - [PSR-0 support](#psr-0-support)
    - [String values](#string-values)
    - [Native functions and constants shadowing](#native-functions-shadowing)
    - [Grouped constants whitelisting](#grouped-constants-whitelisting)
    - [Composer](#composer)
    - [Composer Plugins](#composer-plugins)
- [Contributing](#contributing)
- [Credits](#credits)


## Installation


### PHAR

The preferred method of installation is to use the PHP-Scoper PHAR, which can
be downloaded from the most recent [Github Release][releases].

As the PHAR is signed, you should also download the matching
`php-scoper.phar.pubkey` to the same location. If you rename `php-scoper.phar`
to `php-scoper`, you should also rename `php-scoper.phar.pubkey` to
`php-scoper.pubkey`.


### Composer

You can install PHP-Scoper with Composer:

```bash
composer global require humbug/php-scoper
```

If you cannot install it because of a dependency conflict or you prefer to
install it for your project, we recommend you to take a look at
[bamarni/composer-bin-plugin][bamarni/composer-bin-plugin]. Example:

```bash
composer require --dev bamarni/composer-bin-plugin
composer bin php-scoper require --dev humbug/php-scoper
```

Keep in mind however that this library is not designed to be extended.


## Usage

```bash
php-scoper add-prefix
```

This will prefix all relevant namespaces in code found in the current working
directory. The prefixed files will be accessible in a `build` folder. You can
then use the prefixed code to build your PHAR.

**Warning**: After prefixing the files, if you are relying on Composer
for the autoloading, dumping the autoloader again is required.

For a more concrete example, you can take a look at PHP-Scoper's build
step in [Makefile](Makefile), especially if you are using Composer as
there are steps both before and after running PHP-Scoper to consider.

Refer to TBD for an in-depth look at scoping and building a PHAR taken from
PHP-Scoper's makefile.


## Configuration

If you need more granular configuration, you can create a `scoper.inc.php` by
running the command `php-scoper init`. A different file/location can be passed
with a `--config` option.

```php
<?php declare(strict_types=1);

// scoper.inc.php

use Isolated\Symfony\Component\Finder\Finder;

return [
    'prefix' => null,                       // string|null
    'finders' => [],                        // Finder[]
    'patchers' => [],                       // callable[]
    'whitelist' => [],                      // string[]
    'whitelist-global-constants' => true,   // bool
];
```


### Prefix

The prefix to be used to isolate the code. If `null` or `'''` is given, then a random
prefix will be automatically be generated.


### Finders and paths

By default when running `php-scoper add-prefix`, it will prefix all relevant
code found in the current working directory. You can however define which
files should be scoped by using [Finders][symfony_finder] in the configuration:

```php
<?php declare(strict_types=1);

// scoper.inc.php

use Isolated\Symfony\Component\Finder\Finder;

return [
    'finders' => [
        Finder::create()->files()->in('src'),
        Finder::create()
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
        Finder::create()->append([
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
couple of end to end tests to validate post-scoped code or PHARs is recommended.

Applying such a change can be achieved by defining a suitable patcher in
`scoper.inc.php`:

```php
<?php declare(strict_types=1);

// scoper.inc.php

return [
    'patchers' => [
        function (string $filePath, string $prefix, string $content): string {
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


### Whitelist

PHP-Scoper's goal is to make sure that all code for a project lies in a 
distinct PHP namespace. However, you may want to share a common API between
the bundled code of your PHAR and the consumer code. For example if you have
a PHPUnit PHAR with isolated code, you still want the PHAR to be able to
understand the `PHPUnit\Framework\TestCase` class.


### Constants from the global namespace whitelisting

By default, PHP-Scoper will not prefix the user defined constants belonging to
the global namespace. You can however change that setting for them to be
prefixed as usual unless explicitely whitelisted:

```php
<?php declare(strict_types=1);

// scoper.inc.php

return [
    'whitelist-global-constants' => false,
];
```


### Classes & Constants whitelisting

You can whitelist classes, interfaces and constants like so like so:

```php
<?php declare(strict_types=1);

// scoper.inc.php

return [
    'whitelist' => [
        'PHPUnit\Framework\TestCase',
        'PHPUNIT_VERSION',
    ],
];
```

This will _not_ work on traits or functions.

The class aliasing mechanism is done like follows:
- Prefix the class or interface as usual
- Append a `class_alias()` statement at the end of the class/interface
  declaration to link the prefixed symbol to the non prefixed one
- Append a `class_exists()` statement right after the autoloader is
  registered to trigger the loading of the method which will ensure the
  `class_alias()` statement is executed

It is done this way to ensure prefixed and whitelisted classes can co-exist
together without breaking the autoloading. The `class_exists()` statements are
dumped in `vendor/scoper-autoload.php`, do not forget to include this file in
favour of `vendor/autoload.php`. This part is however sorted out by [Box][box]
if you are using it with the [`PhpScoper` compactor][php-scoper-integration]. 

The constant aliasing mechanism is done by transforming the constant
declaration into a `define()` statement when this is not already the case.
Note that there is a difference here since `define()` defines a constant at
runtime whereas `const` defines it at compile time. You have a more details
post regarding the differences
[here](https://stackoverflow.com/a/3193704/3902761)


### Namespaces whitelisting

If you want to be more generic and whitelist a whole namespace, you can
do it so like this:

```php
<?php declare(strict_types=1);

// scoper.inc.php

return [
    'whitelist' => [
        'PHPUnit\Framework\*',
    ],
];
```

Now anything under the `PHPUnit\Framework` namespace will not be prefixed.
Note this works as well for the global namespace:

```php
<?php declare(strict_types=1);

// scoper.inc.php

return [
    'whitelist' => [
        '*',
    ],
];
```

Note that this may lead to autoloading issues. Indeed if you have the following package:

```json
{
    "autoload": {
        "psr-4": {
            "PHPUnit\\": "src"
        }
    }
}
```

And whitelist the namespace `PHPUnit\Framework\*`, then the autoloading for this package
will be faulty and will not work. For this to work, the whole package `PHPUnit\*` would
need to be whitelisted.


## Building a Scoped PHAR

### With Box

If you are using [Box](box) to build your PHAR, you can use the existing
[PHP-Scoper integration][php-scoper-integration]. Box will take care of
most of the things for you so you should only have to adjust the PHP-Scoper
configuration to your needs.


### Without Box

#### Step 1: Configure build location and prep vendors

Assuming you do not need any development dependencies, run:

```bash
composer install --no-dev --prefer-dist
```

This will allow you to save time in the scoping process by not
processing unnecessary files.


#### Step 2: Run PHP-Scoper

PHP-Scoper copies code to a new location during prefixing, leaving your original
code untouched. The default location is `./build`. You can change the default
location using the `--output-dir` option. By default, it also generates a random
prefix string. You can set a specific prefix string using the `--prefix` option.
If automating builds, you can set the `--force` option to overwrite any code
existing in the output directory without being asked to confirm.

Onto the basic command assuming default options from your project's root
directory:

```bash
bin/php-scoper add-prefix
```

As there are no path arguments, the current working directory will be scoped to
`./build` in its entirety. Of course, actual prefixing is limited to PHP files,
or PHP scripts. Other files are copied unchanged, though we also need to scope
certain Composer related files.

Speaking of scoping Composer related files... The next step is to dump the
Composer autoloader if we depend on it, so everything works as expected:

```bash
composer dump-autoload --working-dir build --classmap-authoritative
```


## Recommendations

There is 3 things to manage when dealing with isolated PHARs:

- The dependencies: which dependencies are you shipping? Fine controlled ones managed
  with a `composer.lock` or you always ship your application with up to date dependencies?
- The PHAR format: there is some incompatibilities such as `realpath()` which will no
  longer work for the files within the PHAR since the paths are not virtual.
- Isolating the code: due to the dynamic nature of PHP, isolating your dependencies will
  never be a trivial task and as a result you should have some end-to-end test to ensure
  your isolated code is working properly. You will also likely need to configure the
  [whitelists][whitelist] or [patchers][patchers].

As a result, you _should_ have end-to-end tests for your (at the minimum) your released
PHAR.

Since dealing with the 3 issues mentioned above at once can be tedious, it is highly
recommended to have several tests for each steps.

For example you can have a test for both your non-isolated PHAR and your isolated PHAR,
this way you will know which step is causing an issue. If the isolated PHAR is not working,
you can try to test the isolated code directly outside the PHAR to make sure the scoping
process is not the issue.

To check if the isolated code is working correctly, you have a number of solutions:

- When using PHP-Scoper directly, by default PHP-Scoper dump the files in a `build` directory.
  Do not forget that
  [you need to dump the Composer autoloader for the isolated code to work!](#step-2-run-php-scoper).
- When using [Box][box], you can use its `--debug` option from the `compile` command in order to have the
  code shipped in the PHAR dumped in the `.box` directory.
- When using a PHAR (created by [Box][box] or any other PHAR building tool), you can use the
  [`Phar::extractTo()`][phar-extract-to] method.

Also take into consideration that bundling code in a PHAR is not guaranteed to work
out of the box either. Indeed there is a number of things such as 

For this reason, you should also h


## Limitations

### PSR-0 support

With the following `composer.json` autoloading configuration:

```json
{
    "autoload": {
        "psr-0": {
            "Foo": "src/"
        }
    }
}
```

If following PSR-0, with the expected file structure is:

```
src/
    Foo/
        A.php
        B.php
```

However this also works:

```
src/
    Foo.php
```

This is unexpected as `Foo` is a file rather than a directory.

PHP-Scoper supports PSR-0 by transforming the configuration into a PSR-4 configuration.
However support a case like above would require to scan the file structure which would
add a significant overhead besides being more complex. As a result PHP-Scoper do not
support the exotic case above.


### String values

PHP-Scoper tries whenever possible to prefix strings as well:

```php
class_exists('Acme\Foo');

// Will be prefixed into:

\class_exists('Humbug\Acme\Foo');
```

PHP-Scoper uses a regex to determine if the string is a class name that must be prefixed. But there is
bound to have confusing cases. For example:

- If you have a plain string `'Acme\Foo'` which has nothing to do with a class, PHP-Parser will not be
  able to tell and will prefix it
- Classes belonging to the global scope: `'Acme_Foo'`, because there is no way to know if it is a class
  name or a random string.


### Native functions and constants shadowing

In the following example:

```php
<?php

namespace Foo;

is_array([]);

```

No use statement is used for the function `is_array`. This means that PHP will try to load the function `\Foo\is_array`
and if fails to do so will fallback on `\is_array` (note that PHP does so only for functions and constants: not classes).

In order to bring some performance optimisation, the call will nonetheless be prefixed in `\is_array`. This *will* break
your code if you were relying on `\Foo\is_array` instead. This however should be _extremely_ rare, so if that happens
you have two solutions: use a [patcher](#patchers) or simpler remove any ambiguity by making use of a use statement
(which is unneeded outside of the context of prefixing your code):

```php
<?php

namespace Foo;

use function Foo\is_array;

is_array([]);

```

The situation is exactly the same for constants.


### Grouped constants whitelisting

When a grouped constant declaration like the following is given:

```php
const X = 'foo', Y = 'bar';
```

PHP-Scoper will not be able to whitelist either `X` or `Y`. The statement
above should be replaced by multiple constant statements:

```php
const X = 'foo';
const Y = 'bar';
```


### Composer

PHP-Scoper does not support prefixing the dumped Composer autoloader and autoloading files. This is why you have to
manually dump the autoloader again after prefixing an application.


### Composer Plugins

Composer plugins are not supported. The issue is that for [whitelisting symbols](#whitelist) PHP-Scoper relies on the
fact that you should load the `vendor/scoper-autoload.php` file instead of `vendor/autoload.php` to trigger the loading
of the right classes with their class aliases. However Composer does not do that and as a result interfaces such as
`Composer\Plugin\Capability\Capable` are prefixed but the alias is not registered. 

This cannot be changed easily so for now when you are using an isolated version of Composer, you will need to use the
`--no-plugins` option.


## Contributing

[Contribution Guide](CONTRIBUTING.md)


## Credits

Project originally created by: [Bernhard Schussek] ([@webmozart]) which has
now been moved under the
[Humbug umbrella][humbug].


[@webmozart]: https://twitter.com/webmozart
[bamarni/composer-bin-plugin]: https://github.com/bamarni/composer-bin-plugin
[Bernhard Schussek]: https://webmozart.io/
[box]: https://github.com/humbug/box
[humbug]: https://github.com/humbug
[releases]: https://github.com/humbug/php-scoper/releases
[symfony_finder]: https://symfony.com/doc/current/components/finder.html
[releases]: https://github.com/humbug/php-scoper/releases
[whitelist]: #whitelist
[patchers]: #patchers
[php-scoper-integration]: https://github.com/humbug/box#isolating-the-phar
[phar-extract-to]: https://secure.php.net/manual/en/phar.extractto.php
