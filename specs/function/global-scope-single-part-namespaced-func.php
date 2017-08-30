<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'single-part namespaced function call in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As there is no namespaced PHP core functions, we can safely prefix.
    'two-parts' =>  <<<'PHP'
<?php

PHPUnit\main();
----
<?php

\Humbug\PHPUnit\main();

PHP
    ,

    // As there is no namespaced PHP core functions, we can safely prefix.
    'FQ two-parts' =>  <<<'PHP'
<?php

\PHPUnit\main();
----
<?php

\Humbug\PHPUnit\main();

PHP
    ,

    // Whitelisting a function has no effect
    'whitelisted two-parts' =>  [
        'whitelist' => ['PHPUnit\main'],
        'payload' => <<<'PHP'
<?php

PHPUnit\main();
----
<?php

\Humbug\PHPUnit\main();

PHP
    ],

    'FQ whitelisted two-parts' =>  [
        'whitelist' => ['PHPUnit\main'],
        'payload' => <<<'PHP'
<?php

\PHPUnit\main();
----
<?php

\Humbug\PHPUnit\main();

PHP
    ],
];
