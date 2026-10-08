# PHP-Scoper

[![Package version](https://img.shields.io/packagist/v/humbug/php-scoper.svg?style=flat-square)](https://packagist.org/packages/humbug/php-scoper)
[![Build Status](https://img.shields.io/github/actions/workflow/status/humbug/php-scoper/tests.yaml?branch=main&style=flat-square)](https://github.com/humbug/php-scoper/actions/workflows/tests.yaml)
[![License](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](LICENSE)

PHP-Scoper moves any body of code, including all its dependencies such as
vendor directories, to a new and distinct namespace.


## Goal

PHP-Scoper's goal is to ensure that all the code of a project lies in a
distinct PHP namespace. This is necessary, for example, when building PHARs that:

- bundle their own vendor dependencies; and
- load or execute code from arbitrary PHP projects with similar dependencies.

When a package, possibly in different versions, is found both in a PHAR and in
the executed code, the one from the PHAR is used. Such PHARs therefore risk
conflicts between their bundled dependencies and those of the project they
interact with. Due to mismatched or unsupported package versions, the
resulting issues can be very difficult to debug.


## Table of Contents

- [Installation](docs/installation.md#installation)
    - [PHAR](docs/installation.md#phar)
    - [Phive](docs/installation.md#phive)
    - [Composer](docs/installation.md#composer)
    - [Docker](docs/installation.md#docker)
- [Usage](#usage)
- [Configuration](docs/configuration.md#configuration)
    - [Prefix](docs/configuration.md#prefix)
    - [PHP Version](docs/configuration.md#php-version)
    - [Output directory](docs/configuration.md#output-directory)
    - [Finders and paths](docs/configuration.md#finders-and-paths)
    - [Patchers](docs/configuration.md#patchers)
    - [Excluded files](docs/configuration.md#excluded-files)
    - [Excluded Symbols](docs/configuration.md#excluded-symbols)
    - [Excluding namespaces](docs/configuration.md#excluding-namespaces)
    - [Exposed Symbols](docs/configuration.md#exposed-symbols)
        - [Exposing namespaces](docs/configuration.md#exposing-namespaces)
        - [Exposing classes](docs/configuration.md#exposing-classes)
        - [Exposing functions](docs/configuration.md#exposing-functions)
        - [Exposing constants](docs/configuration.md#exposing-constants)
- [Building a scoped PHAR](#building-a-scoped-phar)
    - [With Box](#with-box)
    - [Without Box](#without-box)
        - [Step 1: Configure build location and prep vendors](#step-1-configure-build-location-and-prep-vendors)
        - [Step 2: Run PHP-Scoper](#step-2-run-php-scoper)
- [Recommendations](#recommendations)
- [Debugging](#debugging)
- [Further Reading](docs/further-reading.md#further-reading)
    - [How to deal with unknown third-party symbols](docs/further-reading.md#how-to-deal-with-unknown-third-party-symbols)
    - [Autoload aliases](docs/further-reading.md#autoload-aliases)
        - [Class aliases](docs/further-reading.md#class-aliases)
        - [Function aliases](docs/further-reading.md#function-aliases)
    - [Laravel support](docs/further-reading.md#laravel-support)
    - [Symfony support](docs/further-reading.md#symfony-support)
    - [Wordpress support](docs/further-reading.md#wordpress-support)
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
    - [Exposing/Excluding traits](docs/limitations.md#exposingexcluding-traits)
    - [Exposing/Excluding enums](docs/limitations.md#exposingexcluding-enums)
    - [Declaring a custom namespaced function `function_exists()`](docs/limitations.md#declaring-a-custom-namespaced-function-function_exists)
- [Architecture](docs/architecture.md#architecture)
    - [Scopers](docs/architecture.md#scopers)
    - [Scoping PHP files](docs/architecture.md#scoping-php-files)
    - [Reflector](docs/architecture.md#reflector)
    - [Exposed symbols and the scoper autoload](docs/architecture.md#exposed-symbols-and-the-scoper-autoload)
- [Contributing](CONTRIBUTING.md#contributing)
    - [Commands](CONTRIBUTING.md#commands)
    - [Tests](CONTRIBUTING.md#tests)
- [Credits](#credits)


## Usage

```bash
php-scoper add-prefix
```

This prefixes all the relevant namespaces of the code found in the current
working directory. The prefixed files are written to a `build` directory, and
can then be used to build your PHAR.

**Warning**: if you rely on Composer for autoloading, you must dump the
autoloader again after prefixing the files.

For a more concrete example, refer to PHP-Scoper's build step in the
[Makefile](Makefile). This is particularly relevant if you use Composer, as
there are steps to consider both before and after running PHP-Scoper.

## Building a Scoped PHAR

### With Box

If you use [Box][box] to build your PHAR, you can rely on its
[PHP-Scoper integration][php-scoper-integration]. Box takes care of most of
the process, so you should only need to adjust the PHP-Scoper configuration to
your needs.


### Without Box

#### Step 1: Configure build location and prep vendors

Assuming you do not need any development dependencies, run:

```bash
composer install --no-dev --prefer-dist
```

This saves time during scoping, as unnecessary files are not processed.


#### Step 2: Run PHP-Scoper

PHP-Scoper copies the code to a new location during prefixing, leaving your
original code untouched. The default location is `./build`, which can be
changed with the `--output-dir` option. By default, PHP-Scoper also generates a
random prefix, which can be set explicitly with the `--prefix` option. When
automating builds, use the `--force` option to overwrite any existing code in
the output directory without a confirmation prompt.

The basic command, with the default options, run from your project's root
directory is:

```bash
bin/php-scoper add-prefix
```

As no path argument is given, the entire current working directory is scoped
to `./build`. Prefixing is limited to PHP files and scripts; other files are
copied unchanged, with the exception of certain Composer-related files, which
are also scoped.

If you depend on the Composer autoloader, the next step is to dump it so that
everything works as expected:

```bash
composer dump-autoload --working-dir build --classmap-authoritative
```


## Recommendations

There are three aspects to manage when dealing with isolated PHARs:

- The PHAR format: some functions are incompatible with it, such as
  `realpath()`, which no longer works for files within the PHAR as their paths
  are virtual.
- Code isolation: due to the dynamic nature of PHP, isolating your dependencies
  is never trivial. You should therefore have end-to-end tests to ensure your
  isolated code works correctly. You will also likely need to configure the
  excluded and exposed symbols, or [patchers][patchers].
- The dependencies: which dependencies do you ship? Tightly controlled ones,
  managed with a `composer.lock`, or always the latest versions? The latter,
  although preferable, is by design more brittle, as any new release of a
  dependency may break something. Even if the changes are SemVer compliant, the
  code is isolated and shipped in a PHAR.

Consequently, you _should_ have end-to-end tests for, at a minimum, your
released PHAR.

As addressing all three aspects at once can be tedious, it is highly
recommended to have separate tests for each step.

For example, you can test both your non-isolated PHAR and your isolated PHAR to
identify which step causes an issue. If the isolated PHAR does not work, you can
test the isolated code directly, outside the PHAR, to rule out the scoping
process.

There are several ways to check whether the isolated code works correctly:

- When using PHP-Scoper directly, the files are dumped in a `build` directory
  by default. Remember that
  [the Composer autoloader must be dumped for the isolated code to work](#step-2-run-php-scoper).
- When using [Box][box], the `--debug` option of the `compile` command dumps
  the code shipped in the PHAR in the `.box` directory.
- When using a PHAR, whether built with [Box][box] or another tool, you can use
  the [`Phar::extractTo()`][phar-extract-to] method.


## Debugging

A breakdown such as the one described in [Recommendations](#recommendations)
helps identify where an issue originates. However, if you are unsure or are
adjusting patchers, you can check the result for a single file without running
the whole scoping process:

```shell
php-scoper inspect path/to/my-file.php
```


## Contributing

[Contribution Guide](CONTRIBUTING.md)


## Credits

The project was originally created by [Bernhard Schussek] ([@webmozart]) and
has since moved under the [Humbug umbrella][humbug].


[@webmozart]: https://twitter.com/webmozart
[Bernhard Schussek]: https://webmozart.io/
[box]: https://github.com/box-project/box
[humbug]: https://github.com/humbug
[patchers]: docs/configuration.md#patchers
[php-scoper-integration]: https://github.com/box-project/box/blob/main/doc/code-isolation.md#phar-code-isolation
[phar-extract-to]: https://secure.php.net/manual/en/phar.extractto.php
