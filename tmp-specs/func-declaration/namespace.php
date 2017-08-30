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
        'title' => 'function declaration in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
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

    'with use statements' => [
        'whitelist' => ['X\Y'],
        'payload' => <<<'PHP'
<?php

namespace Pi;

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
);

----
<?php

namespace Humbug\Pi;

use Foo;
use Humbug\AppKernel;

function foo(
    \Foo $arg0,
    \Foo $arg1,
    \Humbug\Foo\Bar $arg2,
    \Humbug\Foo\Bar $arg3,
    AppKernel $arg4,
    \Humbug\AppKernel $arg5,
    X\Y $arg6,
    \X\Y $arg7
);

PHP
    ],

    'with use statements and alias' => [
        'whitelist' => ['X\Y'],
        'payload' => <<<'PHP'
<?php

namespace Pi;

use Foo as A;
use AppKernel as B;

function foo(
    A $arg0,
    \A $arg1,
    A\Bar $arg2,
    \A\Bar $arg3,
    B $arg4,
    \B $arg5,
    X\Y $arg6,
    \X\Y $arg7
);

----
<?php

namespace Pi;

use Foo as A;
use Humbug\AppKernel as B;

function foo(
    \Foo $arg0,
    \Foo $arg1,
    \Humbug\Foo\Bar $arg2,
    \Humbug\Foo\Bar $arg3,
    B\AppKernel $arg4,
    \Humbug\AppKernel $arg5,
    X\Y $arg6,
    \X\Y $arg7
);

PHP
    ],
];
