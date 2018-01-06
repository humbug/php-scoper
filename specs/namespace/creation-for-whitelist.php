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
        'whitelist' => [
        ],
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

define( "MY_DEFINE", "value" );

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
];
