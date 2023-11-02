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

## GitHub

You may download the Box PHAR directly from the [GitHub release][releases] directly.
You should however beware that it is not as secure as downloading it from the other mediums.
Hence, it is recommended to check the signature when doing so:

```shell
# Do adjust the URL based on the latest release
wget -O box.phar "https://github.com/humbug/php-scoper/releases/download/0.18.4/php-scoper.phar"
wget -O box.phar.asc "https://github.com/humbug/php-scoper/releases/download/0.18.4/php-scoper.phar.asc"

# Check that the signature matches
gpg --verify php-scoper.phar.asc php-scoper.phar

# Check the issuer (the ID can also be found from the previous command)
gpg --keyserver hkps://keys.openpgp.org --recv-keys 74A754C9778AA03AA451D1C1A000F927D67184EE

rm php-scoper.phar.asc
chmod +x php-scoper.phar
```


<br />
<hr />

« [Table of Contents](../README.md#table-of-contents) • [Configuration](configuration.md#configuration) »


[composer]: https://getcomposer.org
[bamarni/composer-bin-plugin]: https://github.com/bamarni/composer-bin-plugin
[phive]: https://github.com/phar-io/phive
[releases]: https://github.com/humbug/php-scoper/releases
