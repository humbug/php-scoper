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
        'title' => 'Function declarations in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Function declaration in a namespace:
- prefix the namespace
- prefix each argument
- do not prefix whitelisted classes
SPEC
        ,
        'whitelist' => ['X\Y'],
        'payload' => <<<'PHP'
<?php

namespace Pi;

function foo(
    Foo $arg0,
    \Foo $arg1,
    Foo\Bar $arg2,
    \Foo\Bar $arg3,
    AppKernel $arg4,
    \AppKernel $arg5,
    X\Y $arg6,
    \X\Y $arg7
);

----
<?php

namespace Humbug\Pi;

function foo(
    Foo $arg0,
    \Foo $arg1,
    Foo\Bar $arg2,
    \Humbug\Foo\Bar $arg3,
    AppKernel $arg4,
    \Humbug\AppKernel $arg5,
    X\Y $arg6,
    \X\Y $arg7
);

PHP
    ],
];
