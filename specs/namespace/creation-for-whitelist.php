<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'meta' => [
        'title' => 'Namespace declaration creation for whitelisted classes which belong to the global namespace.',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'Single class should receive namespace' => <<<'PHP'
<?php

class AppKernel
{
}

----
<?php

namespace Humbug;

class AppKernel
{
}

PHP
    ,

    'Single abstract class should receive namespace.' => <<<'PHP'
<?php

abstract class AppKernel
{
}

----
<?php

namespace Humbug;

abstract class AppKernel
{
}

PHP
    ,

    'Final class declaration should be prefixed.' => <<<'PHP'
<?php

final class AppKernel {}
----
<?php

namespace Humbug;

final class AppKernel
{
}

PHP
    ,

    'Interfaces can be whitelisted too.' => <<<'PHP'
<?php

interface AppKernel
{
}

----
<?php

namespace Humbug;

interface AppKernel
{
}

PHP
    ,

    'Multiple classes should all receive namespace in the same file.' => <<<'PHP'
<?php

class AppKernalOther2
{
}

class AppKernel
{
}

class AppKernalOther
{
}

----
<?php

namespace {
    class AppKernalOther2
    {
    }
}
namespace Humbug {
    class AppKernel
    {
    }
}
namespace {
    class AppKernalOther
    {
    }
}

PHP
    ,

    'Multiple interfaces should all receive namespace in the same file.' => <<<'PHP'
<?php

interface AppKernel
{
}

class AppKernalOther
{
}

interface SomeInterface
{
}

----
<?php

namespace Humbug {
    interface AppKernel
    {
    }
}
namespace {
    class AppKernalOther
    {
    }
}
namespace {
    interface SomeInterface
    {
    }
}

PHP
    ,

    'Defines should be wrapped in namespace alongside whitelisted class.' => <<<'PHP'
<?php

define("MY_DEFINE", "value");

class AppKernel
{
}

----
<?php

namespace {
    \define("MY_DEFINE", "value");
}
namespace Humbug {
    class AppKernel
    {
    }
}

PHP
    ,

    'Make sure anonymous classes are not wrapped.' => <<<'PHP'
<?php

new class {};

class AppKernel
{
}

----
<?php

namespace {
    new class
    {
    };
}
namespace Humbug {
    class AppKernel
    {
    }
}

PHP
    ,

    'Make sure traits are not prefixed.' => <<<'PHP'
<?php

trait AppKernel
{
}

----
<?php

trait AppKernel
{
}

PHP
    ,

    'Make sure traits are not prefixed next to whitelisted class.' => <<<'PHP'
<?php

trait SomeTrait
{
}

class AppKernel
{
}

----
<?php

namespace {
    trait SomeTrait
    {
    }
}
namespace Humbug {
    class AppKernel
    {
    }
}

PHP
    ,

    'Traits in different namespace.' => <<<'PHP'
<?php

namespace Foo {
    trait SomeTrait{}
}

namespace {
    class AppKernel{}
}

namespace {
    class Bla{}
}

----
<?php

namespace Humbug\Foo {
    trait SomeTrait
    {
    }
}
namespace Humbug {
    class AppKernel
    {
    }
}
namespace {
    class Bla
    {
    }
}

PHP
];
