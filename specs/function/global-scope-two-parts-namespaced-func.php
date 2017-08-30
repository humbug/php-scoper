<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'two-parts namespaced function call in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As there is no namespaced PHP core functions, we can safely prefix.
    'two-parts' =>  <<<'PHP'
<?php

PHPUnit\Command\main();
----
<?php

\Humbug\PHPUnit\Command\main();

PHP
    ,

    // As there is no namespaced PHP core functions, we can safely prefix.
    'FQ two-parts' =>  <<<'PHP'
<?php

\PHPUnit\Command\main();
----
<?php

\Humbug\PHPUnit\Command\main();

PHP
    ,

    // Whitelisting a function has no effect
    'whitelisted two-parts' =>  [
        'whitelist' => ['PHPUnit\Command\main'],
        'payload' => <<<'PHP'
<?php

PHPUnit\Command\main();
----
<?php

\Humbug\PHPUnit\Command\main();

PHP
    ],

    'FQ whitelisted two-parts' =>  [
        'whitelist' => ['PHPUnit\Command\main'],
        'payload' => <<<'PHP'
<?php

\PHPUnit\Command\main();
----
<?php

\Humbug\PHPUnit\Command\main();

PHP
    ],
];
