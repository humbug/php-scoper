# Installation

1. [PHAR](#phar)
1. [Phive](#phive)
1. [Composer](#composer)
1. [Docker](#docker)

## PHAR

The preferred method of installation is to use the PHP-Scoper PHAR which can be
downloaded from the most recent [GitHub Release][releases]. This method ensures
you will not have any dependency conflict issue.

When downloading the PHAR directly, it is recommended to check its signature:

```shell
# Do adjust the URL based on the latest release
wget -O php-scoper.phar "https://github.com/humbug/php-scoper/releases/download/0.18.4/php-scoper.phar"
wget -O php-scoper.phar.asc "https://github.com/humbug/php-scoper/releases/download/0.18.4/php-scoper.phar.asc"

# Check that the signature matches
gpg --verify php-scoper.phar.asc php-scoper.phar

# Check the issuer (the ID can also be found from the previous command)
gpg --keyserver hkps://keys.openpgp.org --recv-keys 74A754C9778AA03AA451D1C1A000F927D67184EE

rm php-scoper.phar.asc
chmod +x php-scoper.phar
```


## Phive

You can install PHP-Scoper with [Phive][phive]

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


## Docker

The official docker image for the project is [`humbugphp/php-scoper`][docker-image]:

```shell
docker pull humbugphp/php-scoper
```


<br />
<hr />

« [Table of Contents](../README.md#table-of-contents) • [Configuration](configuration.md#configuration) »


[composer]: https://getcomposer.org
[docker-image]: https://hub.docker.com/r/humbugphp/php-scoper
[bamarni/composer-bin-plugin]: https://github.com/bamarni/composer-bin-plugin
[phive]: https://github.com/phar-io/phive
[releases]: https://github.com/humbug/php-scoper/releases
