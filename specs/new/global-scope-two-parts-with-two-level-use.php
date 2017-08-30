<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'two-parts new statements in the global scope with a two-level use statement',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'two-parts' =>  <<<'PHP'
<?php

use X\Foo;

new Foo\Bar();
----
<?php

use Humbug\X\Foo;

new Foo\Bar();

PHP
    ,

    'FQ two-parts' =>  <<<'PHP'
<?php

use X\Foo;

new \X\Foo\Bar();
----
<?php

use Humbug\X\Foo;

new \Humbug\X\Foo\Bar();

PHP
    ,

    // If is whitelisted is made into a FQCN to avoid autoloading issues
    'whitelisted two-parts' =>  [
        'whitelist' => ['X\Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use X\Foo;

new Foo\Bar();
----
<?php

use Humbug\X\Foo;

new \X\Foo\Bar();

PHP
    ],

    'FQ whitelisted two-parts' =>  [
        'whitelist' => ['X\Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use X\Foo;

new \X\Foo\Bar();
----
<?php

use Humbug\X\Foo;

new \X\Foo\Bar();

PHP
    ],
];
