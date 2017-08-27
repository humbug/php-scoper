<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Use statements for functions',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As it is extremely rare to use a `use function` statement for a built-in function from the
    // global scope, we can relatively safely assume it is a user-land declare method which should
    // be prefixed.
    'function form the global scope' => <<<'PHP'
<?php

use function foo;

----
<?php

use function Humbug\foo;

PHP
    ,

    // As it is extremely rare to use a `use function` statement for a built-in function from the
    // global scope, we can relatively safely assume it is a user-land declare method which should
    // be prefixed.
    'absolute function form the global scope' => <<<'PHP'
<?php

use function \foo;

----
<?php

use function Humbug\foo;

PHP
    ,

    'already prefixed function form the global scope' => <<<'PHP'
<?php

use function Humbug\foo;

----
<?php

use function Humbug\foo;

PHP
    ,

    'already prefixed absolute function form the global scope' => <<<'PHP'
<?php

use function \Humbug\foo;

----
<?php

use function Humbug\foo;

PHP
    ,

    'namespaced function' => <<<'PHP'
<?php

use function Foo\bar;

----
<?php

use function Humbug\Foo\bar;

PHP
    ,

    'absolute namespaced function' => <<<'PHP'
<?php

use function \Foo\bar;

----
<?php

use function Humbug\Foo\bar;

PHP
    ,

    'already prefixed namespaced function' => <<<'PHP'
<?php

use function Humbug\Foo\bar;

----
<?php

use function Humbug\Foo\bar;

PHP
    ,

    'already prefixed absolute namespaced function' => <<<'PHP'
<?php

use function \Humbug\Foo\bar;

----
<?php

use function Humbug\Foo\bar;

PHP
    ,

    // Whitelist is for classes so this won't have any effect whatsoever
    'whitelisted namespaced function' => [
        'whitelist' => ['Foo\bar'],
        'payload' => <<<'PHP'
<?php

use function Foo\bar;

----
<?php

use function Humbug\Foo\bar;

PHP
    ],
];
