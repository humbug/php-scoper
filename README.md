PHP-Scoper
==========

[![Build Status](https://travis-ci.org/webmozart/php-scoper.svg?branch=master)](https://travis-ci.org/webmozart/php-scoper)
[![Build status](https://ci.appveyor.com/api/projects/status/n06gckamgc2lr8vl/branch/master?svg=true)](https://ci.appveyor.com/project/webmozart/cli/branch/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webmozart/php-scoper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webmozart/php-scoper/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/webmozart/php-scoper/v/stable.svg)](https://packagist.org/packages/webmozart/php-scoper)
[![Total Downloads](https://poser.pugx.org/webmozart/php-scoper/downloads.svg)](https://packagist.org/packages/webmozart/php-scoper)
[![Dependency Status](https://www.versioneye.com/php/webmozart:php-scoper/1.0.0/badge.svg)](https://www.versioneye.com/php/webmozart:php-scoper/1.0.0)

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

These PHARs run the risk raising conflicts between their bundled vendors and the
vendors of the loaded project, if the vendors are required in incompatible
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

If you are having problems, send a mail to bschussek@gmail.com or shout out to
[@webmozart] on Twitter.

License
-------

All contents of this package are licensed under the [MIT license].

[Bernhard Schussek]: http://webmozarts.com
[The Community Contributors]: https://github.com/webmozart/php-scoper/graphs/contributors
[issue tracker]: https://github.com/webmozart/php-scoper/issues
[Git repository]: https://github.com/webmozart/php-scoper
[@webmozart]: https://twitter.com/webmozart
[MIT license]: LICENSE
