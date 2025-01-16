## Further Reading

- [How to deal with unknown third-party symbols](#how-to-deal-with-unknown-third-party-symbols)
- [Autoload aliases](#autoload-aliases)
  - [Class aliases](#class-aliases)
  - [Function aliases](#function-aliases)
- [Laravel support](#laravel-support)
- [Symfony support](#symfony-support)
- [WordPress support](#wordpress-support)


### How to deal with unknown third-party symbols

If you consider the following code:

```php
<?php

namespace Acme;

use function wp_list_users;

foreach (wp_list_users() as $user) {
    // ...
}
```

It would be scoped as follows:

```php
<?php

namespace ScopingPrefix\Acme;

use function ScopingPrefix\wp_list_users;

foreach (wp_list_users() as $user) {
    // ...
}
```

This however will be a problem if your code (or your vendor) never declares
`wp_list_users`.

There is "two" ways to deal with this:

- excluding the symbol (recommended)
- exposing the symbol (fragile)

**Excluding** the symbol (see [excluded-symbols]) marks it as "internal", as if this
symbol was coming from PHP itself or a PHP extension. This is the most appropriate
solution.

**Exposing** the symbol _may_ work but is more fragile. Indeed, exposing the
symbol will result in an alias being registered (see [exposed-symbols]), which
means you _need_ to have the function declared within your codebase at some point.


### Autoload aliases

#### Class aliases

When [exposing a class], an alias will be registered.

#### Function aliases

When [exposing a function] or when a globally declared [excluded-function]
declaration is found (see [#706]), an alias will be registered.


### Laravel support

PHP-Scoper supports laravel out of the box for the most part. There is one problematic piece that is not
supported and that is the views. However, this can be fixed by hand without too much problems:

```php
// scoper.inc.php
<?php declare(strict_types=1);

/** @var Symfony\Component\Finder\Finder $finder */
$finder = Isolated\Symfony\Component\Finder\Finder::class;

$consoleViewFiles = array_map(
    static fn (SplFileInfo $fileInfo) => $fileInfo->getPathname(),
    iterator_to_array(
        $finder::create()
            ->in('vendor/laravel/framework/src/Illuminate/Console/resources/views')
            ->files(),
        false,
    ),
);

return [
    'exclude-files' => [
        ...$consoleViewFiles,
    ],
    'patchers' => [
        static function (string $filePath, string $prefix, string $contents): string {
            if (!str_ends_with($filePath, 'vendor/laravel/framework/src/Illuminate/Console/View/Components/Factory.php')) {
                return $contents;
            }

            return str_replace(
                '$component = \'\\\\Illuminate\\\\Console\\\\View\\\\Components\\\\\' . ucfirst($method);',
                '$component = \'\\\\'.$prefix.'\\\\Illuminate\\\\Console\\\\View\\\\Components\\\\\' . ucfirst($method);',
                $contents,
            );
        },
    ],
];
```


### Symfony Support

When using [PHP configuration][symfony-php-config] files for your services, some elements may not be prefixed correctly
due to being strings. For example (taken directly from the Symfony docs):

```php
<?php // config/services.php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function(ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    // makes classes in src/ available to be used as services
    // this creates a service per class whose id is the fully-qualified class name
    $services->load('App\\', '../src/')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}');

    // order is important in this file because service definitions
    // always *replace* previous ones; add your own service configuration below
};
```

The string `'App\\'` from `$services->load()` will not be made into `'Prefix\\App\\'`. To address this
you need to use [patchers]. Alternatively, PHP-Scoper provides one which should should handle such cases:

```php
<?php // scoper.inc.php

$symfonyPatcher = (require __DIR__.'/vendor/humbug/php-scoper/res/create-symfony-php-services-patcher.php')('config/services.php');

return [
    'patchers' => [$symfonyPatcher],
    // ...
];
```

Note that the path is the "regular path(s)" that can be passed to patchers.


### WordPress Support

When writing a WordPress plugin, you need to [exclude WordPress' symbols](#excluded-symbols). To facilitate
this task, [Snicco] created a third-party CLI tool [php-scoper-excludes] that can be used to generate
PHP-Scoper compatible symbol lists for any PHP codebase you point it.

### Example for WordPress Core

```shell
composer require sniccowp/php-scoper-wordpress-excludes
```

```php
// scoper.inc.php

function getWpExcludedSymbols(string $fileName): array
{
    $filePath = __DIR__.'/vendor/sniccowp/php-scoper-wordpress-excludes/generated/'.$fileName;

    return json_decode(
        file_get_contents($filePath),
        true,
    );
}

$wpConstants = getWpExcludedSymbols('exclude-wordpress-constants.json');
$wpClasses = getWpExcludedSymbols('exclude-wordpress-classes.json');
$wpFunctions = getWpExcludedSymbols('exclude-wordpress-functions.json');


return [
  'exclude-constants' => $wpConstants,
  'exclude-classes' => $wpClasses,
  'exclude-functions' => $wpFunctions,
  // ...
];
```


<br />
<hr />

« [Configuration](configuration.md#configuration) • [Limitations](limitations.md#limitations) »

[excluded-function]: configuration.md#excluded-symbols
[excluded-symbols]: configuration.md#excluded-symbols
[excluding a function]: configuration.md#excluded-symbols
[exposed-symbols]: configuration.md#exposed-symbols
[exposing a class]: configuration.md#exposing-classes
[exposing a function]: configuration.md#exposing-functions
[#706]: https://github.com/humbug/php-scoper/pull/706
[Snicco]: https://github.com/snicco
[symfony-php-config]: https://symfony.com/doc/current/service_container.html#explicitly-configuring-services-and-arguments
[patchers]: ./configuration.md#patchers
[php-scoper-excludes]: https://github.com/snicco/php-scoper-excludes
