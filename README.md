The Puli Command Line Interface
===============================

[![Build Status](https://travis-ci.org/webmozart/php-scoper.svg?branch=master)](https://travis-ci.org/webmozart/php-scoper)
[![Build status](https://ci.appveyor.com/api/projects/status/n06gckamgc2lr8vl/branch/master?svg=true)](https://ci.appveyor.com/project/webmozart/cli/branch/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webmozart/php-scoper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webmozart/php-scoper/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/webmozart/php-scoper/v/stable.svg)](https://packagist.org/packages/webmozart/php-scoper)
[![Total Downloads](https://poser.pugx.org/webmozart/php-scoper/downloads.svg)](https://packagist.org/packages/webmozart/php-scoper)
[![Dependency Status](https://www.versioneye.com/php/puli:cli/1.0.0/badge.svg)](https://www.versioneye.com/php/puli:cli/1.0.0)

Latest release: [1.0.0-beta9](https://packagist.org/packages/webmozart/php-scoper#1.0.0-beta9)

PHP >= 5.3.9

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

Installation
------------

Follow the [Getting Started] guide to install Puli in your project.

Documentation
-------------

Read the [Puli Documentation] to learn more about Puli.

Contribute
----------

Contributions to are very welcome!

* Report any bugs or issues you find on the [issue tracker].
* You can grab the source code at Puli’s [Git repository].

Support
-------

If you are having problems, send a mail to bschussek@gmail.com or shout out to
[@webmozart] on Twitter.

License
-------

All contents of this package are licensed under the [MIT license].

[Puli]: http://puli.io
[Puli Manager]: https://github.com/puli/manager
[Bernhard Schussek]: http://webmozarts.com
[The Community Contributors]: https://github.com/webmozart/php-scoper/graphs/contributors
[Getting Started]: http://docs.puli.io/en/latest/getting-started.html
[Puli Documentation]: http://docs.puli.io/en/latest/index.html
[Puli at a Glance]: http://docs.puli.io/en/latest/at-a-glance.html
[issue tracker]: https://github.com/puli/issues/issues
[Git repository]: https://github.com/webmozart/php-scoper
[@webmozart]: https://twitter.com/webmozart
[MIT license]: LICENSE
