<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'global function call in the global scope with single-level use statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As it is extremely rare to use a `use function` statement for a built-in function from the
    // global scope, we can relatively safely assume it is a user-land declare method which should
    // be prefixed.
    'single-part' =>  <<<'PHP'
<?php

use function main;

main();
----
<?php

use function Humbug\main;

main();

PHP
    ,

    // As it is extremely rare to use a `use function` statement for a built-in function from the
    // global scope, we can relatively safely assume it is a user-land declare method which should
    // be prefixed.
    'FQ single-part' =>  <<<'PHP'
<?php

use function main;

\main();
----
<?php

use function Humbug\main;

\main();

PHP
    ,
];
