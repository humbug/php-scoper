<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'two-parts namespaced constant reference in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As there is no namespaced PHP core functions, we can safely prefix.
    'two-parts' =>  <<<'PHP'
<?php

PHPUnit\Command\DUMMY_CONST;
----
<?php

\Humbug\PHPUnit\Command\DUMMY_CONST;

PHP
    ,

    // As there is no namespaced PHP core functions, we can safely prefix.
    'FQ two-parts' =>  <<<'PHP'
<?php

\PHPUnit\Command\DUMMY_CONST;
----
<?php

\Humbug\PHPUnit\Command\DUMMY_CONST;

PHP
    ,

    // Whitelisting a constant has no effect
    'whitelisted two-parts' =>  [
        'whitelist' => ['PHPUnit\Command\DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

PHPUnit\Command\DUMMY_CONST;
----
<?php

\Humbug\PHPUnit\Command\DUMMY_CONST;

PHP
    ],

    'FQ whitelisted two-parts' =>  [
        'whitelist' => ['PHPUnit\Command\DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

\PHPUnit\Command\DUMMY_CONST;
----
<?php

\Humbug\PHPUnit\Command\DUMMY_CONST;

PHP
    ],
];
