<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'global constant reference in the global scope with single-level use statements and alias',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As it is extremely rare to use a `use constant` statement for a built-in constant from the
    // global scope, we can relatively safely assume it is a user-land declared constant which should
    // be prefixed.
    'single-part' =>  <<<'PHP'
<?php

use constant DUMMY_CONST as FOO;

FOO;
----
<?php

use constant Humbug\DUMMY_CONST as FOO;

FOO;

PHP
    ,

    // As it is extremely rare to use a `use constant` statement for a built-in constant from the
    // global scope, we can relatively safely assume it is a user-land declared constant which should
    // be prefixed.
    'FQ single-part' =>  <<<'PHP'
<?php

use constant DUMMY_CONST as FOO;

\FOO;
----
<?php

use constant Humbug\DUMMY_CONST as FOO;

\FOO;

PHP
    ,
];
