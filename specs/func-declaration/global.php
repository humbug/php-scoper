<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'function declaration in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
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
);

----
<?php

function foo(
    \Foo $arg0,
    \Foo $arg1,
    \Humbug\Foo\Bar $arg2,
    \Humbug\Foo\Bar $arg3,
    \Humbug\AppKernel $arg4,
    \Humbug\AppKernel $arg5,
    \X\Y $arg6,
    \X\Y $arg7
);

PHP
    ],

    'with use statements' => [
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
);

----
<?php

use Foo;
use Humbug\AppKernel;

function foo(
    \Foo $arg0,
    \Foo $arg1,
    \Humbug\Foo\Bar $arg2,
    \Humbug\Foo\Bar $arg3,
    \Humbug\AppKernel $arg4,
    \Humbug\AppKernel $arg5,
    \X\Y $arg6,
    \X\Y $arg7
);

PHP
    ],

    'with use statements and alias' => [
        'whitelist' => ['X\Y'],
        'payload' => <<<'PHP'
<?php

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

use Foo as A;
use Humbug\AppKernel as B;

function foo(
    \Foo $arg0,
    \Foo $arg1,
    \Humbug\Foo\Bar $arg2,
    \Humbug\Foo\Bar $arg3,
    \Humbug\AppKernel $arg4,
    \Humbug\AppKernel $arg5,
    \X\Y $arg6,
    \X\Y $arg7
);

PHP
    ],
];
