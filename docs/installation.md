# Installation

1. [PHAR](#phar)
1. [Phive](#phive)
1. [Composer](#composer)

## PHAR

The preferred method of installation is to use the PHP-Scoper PHAR which can be
downloaded from the most recent [Github Release][releases]. This method ensures
you will not have any dependency conflict issue.


## Phive

You can install Box with [Phive][phive]

```bash
$ phive install humbug/php-scoper --force-accept-unsigned
```

To upgrade `humbug/php-scoper` use the following command:

```bash
$ phive update humbug/php-scoper --force-accept-unsigned
```


## Composer

You can install PHP-Scoper with [Composer][composer]:

```bash
$ composer global require humbug/php-scoper
```

If you cannot install it because of a dependency conflict or you prefer to
install it for your project, it is recommended to take a look at 
[bamarni/composer-bin-plugin][bamarni/composer-bin-plugin]. Example:

```bash
$ composer require --dev bamarni/composer-bin-plugin
$ composer bin php-scoper require --dev humbug/php-scoper

$ vendor/bin/php-scoper
```


<br />
<hr />

« [Table of Contents](../README.md#table-of-contents) • [Configuration](configuration.md#configuration) »


[releases]: https://github.com/humbug/php-scoper/releases
[composer]: https://getcomposer.org
[bamarni/composer-bin-plugin]: https://github.com/bamarni/composer-bin-plugin
[phive]: https://github.com/phar-io/phive
