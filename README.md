# PHP-Scoper

[![Package version](https://img.shields.io/packagist/v/humbug/php-scoper.svg?style=flat-square)](https://packagist.org/packages/humbug/php-scoper)
[![Build Status](https://github.com/humbug/php-scoper/workflows/Build/badge.svg)](https://github.com/humbug/php-scoper/actions)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/humbug/php-scoper.svg?branch=main&style=flat-square)](https://scrutinizer-ci.com/g/humbug/php-scoper/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/humbug/php-scoper/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/humbug/php-scoper/?branch=main)
[![Slack](https://img.shields.io/badge/slack-%23humbug-red.svg?style=flat-square)](https://symfony.com/slack-invite)
[![License](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](LICENSE)

PHP-Scoper is a tool which essentially moves any body of code, including all
dependencies such as vendor directories, to a new and distinct namespace.


## Goal

PHP-Scoper's goal is to make sure that all code for a project lies in a 
distinct PHP namespace. This is necessary, for example, when building PHARs that:

- Bundle their own vendor dependencies; and 
- Load/execute code from arbitrary PHP projects with similar dependencies

When a package (of possibly different versions) exists, and is found in both a PHAR
and the executed code, the one from the PHAR will be used. This means these
PHARs run the risk of raising conflicts between their bundled vendors and the
vendors of the project they are interacting with, leading to issues that are 
potentially very difficult to debug due to dissimilar or unsupported package versions.


## Table of Contents

- [Installation](docs/installation.md#installation)
    - [Phive](docs/installation.md#phive)
    - [PHAR](docs/installation.md#phar)
    - [Composer](docs/installation.md#composer)
- [Usage](#usage)
- [Configuration](docs/configuration.md#configuration)
    - [Prefix](docs/configuration.md#prefix)
    - [Finders and paths](docs/configuration.md#finders-and-paths)
    - [Patchers](docs/configuration.md#patchers)
    - [Excluded files](docs/configuration.md#excluded-files)
    - [Excluded Symbols](docs/configuration.md#excluded-symbols)
    - [Exposed Symbols](docs/configuration.md#exposed-symbols)
        - [Exposing classes](docs/configuration.md#exposing-classes)
        - [Exposing functions](docs/configuration.md#exposing-functions)
        - [Exposing constants](docs/configuration.md#exposing-constants)
- [Building a scoped PHAR](#building-a-scoped-phar)
    - [With Box](#with-box)
    - [Without Box](#without-box)
        - [Step 1: Configure build location and prep vendors](#step-1-configure-build-location-and-prep-vendors)
        - [Step 2: Run PHP-Scoper](#step-2-run-php-scoper)
- [Recommendations](#recommendations)
- [Further Reading](docs/further-reading.md#further-reading)
    - [Polyfills](docs/further-reading.md#polyfills)
- [Limitations](docs/limitations.md#limitations)
    - [Dynamic symbols](docs/limitations.md#dynamic-symbols)
    - [Date symbols](docs/limitations.md#date-symbols)
    - [Heredoc values](docs/limitations.md#heredoc-values)
    - [Callables](docs/limitations.md#callables)
    - [String values](docs/limitations.md#string-values)
    - [Native functions and constants shadowing](docs/limitations.md#native-functions-and-constants-shadowing)
    - [Composer Autoloader](docs/limitations.md#composer-autoloader)
    - [Composer Plugins](docs/limitations.md#composer-plugins)
    - [PSR-0 Partial support](docs/limitations.md#psr-0-partial-support)
    - [Files autoloading](docs/limitations.md#files-autoloading)
- [Contributing](#contributing)
- [Credits](#credits)


## Usage

```bash
php-scoper add-prefix
```

This will prefix all relevant namespaces in code found in the current working
directory. The prefixed files will be accessible in a `build` folder. You can
then use the prefixed code to build your PHAR.

**Warning**: After prefixing the files, if you are relying on Composer
for the auto-loading, dumping the autoloader again is required.

For a more concrete example, you can take a look at PHP-Scoper's build
step in [Makefile](Makefile), especially if you are using Composer as
there are steps both before and after running PHP-Scoper to consider.

Refer to TBD for an in-depth look at scoping and building a PHAR taken from
PHP-Scoper's makefile.


## Building a Scoped PHAR

### With Box

If you are using [Box][box] to build your PHAR, you can use the existing
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

- The PHAR format: there is some incompatibilities such as `realpath()` which
  will no longer work for the files within the PHAR since the paths are not
  virtual.
- Isolating the code: due to the dynamic nature of PHP, isolating your
  dependencies will never be a trivial task and as a result you should have
  some end-to-end test to ensure your isolated code is working properly. You
  will also likely need to configure the excluded and exposed symbols or
  [patchers][patchers].
- The dependencies: which dependencies are you shipping? Fine controlled ones 
  managed with a `composer.lock` or you always ship your application with
  up-to-date dependencies? The latter, although more ideal, will by design
  result in more brittleness as any new release from a dependency may break
  something (although the changes may be SemVer compliant, we are dealing with
  PHARs and isolated code)

As a result, you _should_ have end-to-end tests for your (at the minimum) your 
released PHAR.

Since dealing with the 3 issues mentioned above at once can be tedious, it is
highly recommended having several tests for each step.

For example, you can have a test for both your non-isolated PHAR and your 
isolated PHAR, this way you will know which step is causing an issue. If the 
isolated PHAR is not working, you can try to test the isolated code directly 
outside the PHAR to make sure the scoping process is not the issue.

To check if the isolated code is working correctly, you have a number of solutions:

- When using PHP-Scoper directly, by default PHP-Scoper dump the files in a 
  `build` directory. Do not forget that
  [you need to dump the Composer autoloader for the isolated code to work!](#step-2-run-php-scoper).
- When using [Box][box], you can use its `--debug` option from the `compile` 
  command in order to have the code shipped in the PHAR dumped in the `.box` 
  directory.
- When using a PHAR (created by [Box][box] or any other PHAR building tool), 
  you can use the [`Phar::extractTo()`][phar-extract-to] method.

Also take into consideration that bundling code in a PHAR is not guaranteed to work
out of the box either. Indeed there is a number of things such as 

For this reason, you should also h


## Contributing

[Contribution Guide](CONTRIBUTING.md)


## Credits

Project originally created by: [Bernhard Schussek] ([@webmozart]) which has
now been moved under the
[Humbug umbrella][humbug].


[@webmozart]: https://twitter.com/webmozart
[Bernhard Schussek]: https://webmozart.io/
[box]: https://github.com/humbug/box
[humbug]: https://github.com/humbug
[patchers]: docs/configuration.md#patchers
[php-scoper-integration]: https://github.com/humbug/box#isolating-the-phar
[phar-extract-to]: https://secure.php.net/manual/en/phar.extractto.php
