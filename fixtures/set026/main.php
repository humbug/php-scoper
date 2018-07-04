<?php

declare(strict_types=1);

use Acme\Foo as FooClass;
use const Acme\FOO as FOO_CONST;
use function Acme\foo as foo_func;

if (file_exists($autoload = __DIR__ . '/vendor/scoper-autoload.php')) {
    require_once $autoload;
} else {
    require_once __DIR__ . '/vendor/autoload.php';
}

(new FooClass())();
foo_func();
echo FOO_CONST.PHP_EOL;
