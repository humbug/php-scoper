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
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
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

namespace Humbug;

class AppKernalOther2
{
}
class AppKernel
{
}
class AppKernalOther
{
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

namespace Humbug;

interface AppKernel
{
}
class AppKernalOther
{
}
interface SomeInterface
{
}

PHP
    ,

    'Defines should be wrapped in namespace alongside rest.' => <<<'PHP'
<?php

define("MY_DEFINE", "value");

class AppKernel
{
}

----
<?php

namespace Humbug;

\define("MY_DEFINE", "value");
class AppKernel
{
}

PHP
    ,

    'Make sure anonymous classes are wrapped in a prefixed namespace.' => <<<'PHP'
<?php

new class {};

class AppKernel
{
}

----
<?php

namespace Humbug;

new class
{
};
class AppKernel
{
}

PHP
    ,

    'Make sure traits are wrapped in a prefix namespace.' => <<<'PHP'
<?php

trait AppKernel
{
}

----
<?php

namespace Humbug;

trait AppKernel
{
}

PHP
    ,

    'Traits in different namespace.' => <<<'PHP'
<?php

namespace Foo {
    trait SomeTrait {}
}

namespace {
    class AppKernel {
        use Foo\SomeTrait;
    }
}

namespace {
    use Foo\SomeTrait as X;

    class Bla {
        use X;
    }
}

----
<?php

namespace Humbug\Foo;

trait SomeTrait
{
}
namespace Humbug;

class AppKernel
{
    use Foo\SomeTrait;
}
namespace Humbug;

use Humbug\Foo\SomeTrait as X;
class Bla
{
    use X;
}

PHP
];
