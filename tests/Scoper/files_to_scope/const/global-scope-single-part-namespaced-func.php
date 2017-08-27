<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'single-part namespaced constant reference in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As there is no namespaced PHP core functions, we can safely prefix.
    'two-parts' =>  <<<'PHP'
<?php

PHPUnit\DUMMY_CONST;
----
<?php

\Humbug\PHPUnit\DUMMY_CONST;

PHP
    ,

    // As there is no namespaced PHP core functions, we can safely prefix.
    'FQ two-parts' =>  <<<'PHP'
<?php

\PHPUnit\DUMMY_CONST;
----
<?php

\Humbug\PHPUnit\DUMMY_CONST;

PHP
    ,

    // Whitelisting a constant has no effect
    'whitelisted two-parts' =>  [
        'whitelist' => ['PHPUnit\DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

PHPUnit\DUMMY_CONST;
----
<?php

\Humbug\PHPUnit\DUMMY_CONST;

PHP
    ],

    'FQ whitelisted two-parts' =>  [
        'whitelist' => ['PHPUnit\DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

\PHPUnit\DUMMY_CONST;
----
<?php

\Humbug\PHPUnit\DUMMY_CONST;

PHP
    ],
];
