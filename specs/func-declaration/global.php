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
        'title' => 'Function declarations in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Function declaration in the global namespace:
- prefix each argument
SPEC
        ,
        'whitelist' => ['X\Y'],
        'payload' => <<<'PHP'
<?php

function foo(
    Foo $arg0,
    \Foo $arg1,
    Foo\Bar $arg2,
    \Foo\Bar $arg3,
    AppKernel $arg4,
    \AppKernel $arg5,
    X\Y $arg6,
    \X\Y $arg7
) {
}

----
<?php

function foo(\Foo $arg0, \Foo $arg1, \Humbug\Foo\Bar $arg2, \Humbug\Foo\Bar $arg3, \Humbug\AppKernel $arg4, \Humbug\AppKernel $arg5, \X\Y $arg6, \X\Y $arg7)
{
}

PHP
    ],

    [
        'spec' => <<<'SPEC'
Function declaration in the global namespace with use statements:
- prefix the use appropriate statements
- prefix each argument
- do not prefix whitelisted classes
SPEC
        ,
        'whitelist' => ['X\Y'],
        'payload' => <<<'PHP'
<?php

use Foo;
use AppKernel;

function foo(
    Foo $arg0,
    \Foo $arg1,
    Foo\Bar $arg2,
    \Foo\Bar $arg3,
    AppKernel $arg4,
    \AppKernel $arg5,
    X\Y $arg6,
    \X\Y $arg7
) {
}

----
<?php

use Foo;
use Humbug\AppKernel;
function foo(\Foo $arg0, \Foo $arg1, \Humbug\Foo\Bar $arg2, \Humbug\Foo\Bar $arg3, \Humbug\AppKernel $arg4, \Humbug\AppKernel $arg5, \X\Y $arg6, \X\Y $arg7)
{
}

PHP
    ],
];
