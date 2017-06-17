# PHP-Scoper

[![Package version](https://img.shields.io/packagist/v/humbug/php-scoper.svg?style=flat-square)](https://packagist.org/packages/humbug/php-scoper)
[![Travis Build Status](https://img.shields.io/travis/humbug/php-scoper.svg?branch=master&style=flat-square)](https://travis-ci.org/humbug/php-scoper?branch=master)
[![AppVeyor Build Status](https://img.shields.io/appveyor/ci/humbug/php-scoper.svg?branch=master&style=flat-square)](https://ci.appveyor.com/project/humbug/php-scoper/branch/master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/humbug/php-scoper.svg?branch=master&style=flat-square)](https://scrutinizer-ci.com/g/humbug/php-scoper/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/humbug/php-scoper/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/humbug/php-scoper/?branch=master)
[![Slack](https://img.shields.io/badge/slack-%23humbug-red.svg?style=flat-square)](https://symfony.com/slack-invite)
[![License](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](LICENSE)

PHP-Scoper is a tool for adding a prefix to all PHP namespaces in a given file
or directory. 


## Goal

PHP-Scoper's goal is to make sure that all code in a directory lies in a 
distinct PHP namespace. This is necessary when building PHARs that 

* Bundle their own vendor dependencies
* Load code of arbitrary PHP projects

These PHARs run the risk of raising conflicts between their bundled vendors and 
the vendors of the loaded project, if the vendors are required in incompatible
versions.


## Installation

You can install PHP-Scoper with Composer:

```bash
composer global require humbug/php-scoper:dev-master
```

If you cannot install it because of a dependency conflict or you prefer to
install it for your project, we recommend you to take a look at
[bamarni/composer-bin-plugin][bamarni/composer-bin-plugin]. Example:

```bash
composer require --dev bamarni/composer-bin-plugin
composer bin php-scoper require --dev humbug/php-scoper:dev-master
```

A PHAR should be availaible soon as well.


## Usage

```bash
php-scoper add-prefix
```

This will prefix all the files found in the current working directory.
The prefixed files will be accessible in a `build` folder. You can
then use the prefixed code to build your PHAR.

**Warning**: After prefexing the files, if you are relying on Composer
for the autoloading, dumping the autoloader again is required.

For a more concrete example, you can take a look at PHP-Scoper's build
step in [Makefile](Makefile).


## Contributing

[Contribution Guide](CONTRIBUTING.md)


## Credits

Project originally created by: [Bernhard Schussek] ([@webmozart]) which has then been moved under the
[Humbug umbrella][humbug].


[Bernhard Schussek]: https://webmozart.io/
[@webmozart]: https://twitter.com/webmozart
[humbug]: https://github.com/humbug
[bamarni/composer-bin-plugin]: https://github.com/bamarni/composer-bin-plugin