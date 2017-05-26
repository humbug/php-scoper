PHP-Scoper
==========

[![Build Status](https://travis-ci.org/humbug/php-scoper.svg?branch=master)](https://travis-ci.org/humbug/php-scoper)
[![Build status](https://ci.appveyor.com/api/projects/status/oa95nul9v8uv9emw/branch/master?svg=true)](https://ci.appveyor.com/project/humbug/php-scoper/branch/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/humbug/php-scoper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/humbug/php-scoper/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/humbug/php-scoper/v/stable.svg)](https://packagist.org/packages/humbug/php-scoper)
[![Total Downloads](https://poser.pugx.org/humbug/php-scoper/downloads.svg)](https://packagist.org/packages/humbug/php-scoper)
[![Dependency Status](https://www.versioneye.com/php/humbug:php-scoper/1.0.0/badge.svg)](https://www.versioneye.com/php/humbug:php-scoper/1.0.0)

Latest release: none

PHP >= 5.5

PHP-Scoper is a tool for adding a prefix to all PHP namespaces in a given file
or directory. 

Goal
----

PHP-Scoper's goal is to make sure that all code in a directory lies in a 
distinct PHP namespace. This is necessary when building PHARs that 

* bundle their own vendor dependencies
* load code of arbitrary PHP projects

These PHARs run the risk of raising conflicts between their bundled vendors and 
the vendors of the loaded project, if the vendors are required in incompatible
versions.

Usage
-----

Use PHP-Scoper like this:

```
$ php-scoper add-prefix MyPhar\\ .
```

The first argument is the prefix to add to all namespace declarations and class 
usages. The second argument is one or more files/directories which should be 
processed.

Authors
-------

* [Bernhard Schussek] a.k.a. [@webmozart]
* [The Community Contributors]

Contribute
----------

Contributions to are very welcome!

* Report any bugs or issues you find on the [issue tracker].
* You can grab the source code at PHP-Scoper's [Git repository].

Support
-------

Pending

License
-------

All contents of this package are licensed under the [MIT license].

[The Community Contributors]: https://github.com/humbug/php-scoper/graphs/contributors
[Issue tracker]: https://github.com/humbug/php-scoper/issues
[Git repository]: https://github.com/humbug/php-scoper
[MIT license]: LICENSE

Project originally created by:
[Bernhard Schussek]: http://webmozarts.com
[@webmozart]: https://twitter.com/webmozart