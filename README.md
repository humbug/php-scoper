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
    - [PHAR (preferred)](#phar-preferred)
    - [Composer](#composer)
- [Configuration](#configuration)
    - [Finders and paths](#finders-and-paths)
    - [Patchers](#patchers)
    - [Global Namespace Whitelisting](#global-namespace-whitelisting)
    - [Whitelist](#whitelist)
- [Building A Scoped PHAR](#building-a-scoped-phar)
    - [Step 1: Configure build location and prep vendors](#step-1-configure-build-location-and-prep-vendors)
    - [Step 2: Run PHP-Scoper](#step-2-run-php-scoper)
    - [Step 3: Build, test, and cleanup](#step-3-build-test-and-cleanup)
- [Limitations](#limitations)
    - [PSR-0 support](#psr-0-support)
    - [String values](#string-values)
    - [Native functions and constants shadowing](#native-functions-shadowing)
- [Contributing](#contributing)
- [Credits](#credits)


## Installation


### PHAR (preferred)

The preferred method of installation is to use the PHP-Scoper PHAR, which can
be downloaded from the most recent [Github Release][releases]. Subsequent updates
can be downloaded by running:

```bash
php-scoper.phar self-update
```

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
    'finders' => [],
    'patchers' => [],
    'global_namespace_whitelist' => [],
    'whitelist' => [],
];
```


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

### Global Namespace Whitelisting

By default, PHP-Scoper only scopes (or prefixes) code where the namespace is
non-global. In other words, non-namespaced code is not scoped. This leaves the
majority of classes, functions and constants in PHP, and most extensions,
untouched.

This is not necessarily a desirable outcome for vendor dependencies which are
also not namespaced. To ensure they are isolated, you can configure PHP-Scoper to
allow their prefixing from `scoper.inc.php` using basic strings or callables:

```php
<?php declare(strict_types=1);

// scoper.inc.php

return [
    'global_namespace_whitelist' => [
        'AppKernel',
        function ($className) {
            return 'PHPUnit' === substr($className, 0, 6);
        },
    ],
];
```

In this example, we're ensuring that the `AppKernal` class, and any
non-namespaced PHPUnit packages are prefixed.


### Whitelist

PHP-Scoper's goal is to make sure that all code for a project lies in a 
distinct PHP namespace. However, you may want to share a common API between
the bundled code of your PHAR and the consumer code. For example if you have
a PHPUnit PHAR with isolated code, you still want the PHAR to be able to
understand the `PHPUnit\Framework\TestCase` class.

A way to achieve this is by specifying a list of classes to not prefix:

```php
<?php declare(strict_types=1);

// scoper.inc.php

return [
    'whitelist' => [
        'PHPUnit\Framework\TestCase',
    ],
];
```

Note that only classes are whitelisted, this does not affect constants
or functions.

For whitelist to work, you then require to load `vendor/scoper-autoload.php`
instead of the traditional `vendor/autoload.php`.


## Building A Scoped PHAR

This is a brief run through of the basic steps encoded in PHP-Scoper's own
[Makefile](Makefile) and elsewhere to build a PHAR from scoped code.


### Step 1: Configure build location and prep vendors

If, for example, you are using [Box](box) to build your PHAR, you
should set the `base-path` configuration option in your `box.json` file
to point at the directory which will host scoped code. PHP-Scoper,
by default, creates a `build` directory relative to the current working
directory.

```js
"base-path": "build"
```

Assuming you need no dev dependencies, run:

```bash
composer install --no-dev --prefer-dist
```


### Step 2: Run PHP-Scoper

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
composer dump-autoload -d build --classmap-authoritative
```


### Step 3: Build, test, and cleanup

If using [Box](box), you can now move onto actually building the PHAR:

```bash
php -d phar.readonly=0 box build -vvv
```

At this point, it's best to have some simple end-to-end tests automated to put
the PHAR through its paces and locate any problems (see Patchers and Whitelists
from earlier in this README). Assuming it passes testing, the PHAR is ready.

Cleanup is simply to optionally delete `./build` contents, and remember to
re-install dev dependencies removed during Step 1:

```bash
composer install
``` 


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


## Contributing

[Contribution Guide](CONTRIBUTING.md)


## Credits

Project originally created by: [Bernhard Schussek] ([@webmozart]) which has
now been moved under the
[Humbug umbrella][humbug].


[@webmozart]: https://twitter.com/webmozart
[bamarni/composer-bin-plugin]: https://github.com/bamarni/composer-bin-plugin
[Bernhard Schussek]: https://webmozart.io/
[box]: https://github.com/box-project/box2
[humbug]: https://github.com/humbug
[releases]: https://github.com/humbug/php-scoper/releases
[symfony_finder]: https://symfony.com/doc/current/components/finder.html
[releases]: https://github.com/humbug/php-scoper/releases
