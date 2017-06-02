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


## Usage

Use PHP-Scoper like this:

```
$ php-scoper add-prefix MyPhar\\ .
```

The first argument is the prefix to add to all namespace declarations and class 
usages. The second argument is one or more files/directories which should be 
processed.


## Contributing

[Contribution Guide](CONTRIBUTING.md)


## Credits

Project originally created by: [Bernhard Schussek] ([@webmozart]) which has then been moved under the
[Humbug umbrella][humbug].


[Bernhard Schussek]: https://webmozart.io/
[@webmozart]: https://twitter.com/webmozart
[humbug]: https://github.com/humbug
