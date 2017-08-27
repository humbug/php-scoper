<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'two-parts class constant references in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As there is nothing in PHP core with more than two-parts, we can safely prefix.
    'two-parts' =>  <<<'PHP'
<?php

PHPUnit\Command::MAIN_CONST;
----
<?php

\Humbug\PHPUnit\Command::MAIN_CONST;

PHP
    ,

    // As there is nothing in PHP core with more than two-parts, we can safely prefix.
    'FQ two-parts' =>  <<<'PHP'
<?php

\PHPUnit\Command::MAIN_CONST;
----
<?php

\Humbug\PHPUnit\Command::MAIN_CONST;

PHP
    ,

    // If is whitelisted is made into a FQCN to avoid autoloading issues
    'whitelisted two-parts' =>  [
        'whitelist' => ['PHPUnit\Command'],
        'payload' => <<<'PHP'
<?php

PHPUnit\Command::MAIN_CONST;
----
<?php

\PHPUnit\Command::MAIN_CONST;

PHP
    ],

    'FQ whitelisted two-parts' =>  [
        'whitelist' => ['PHPUnit\Command'],
        'payload' => <<<'PHP'
<?php

\PHPUnit\Command::MAIN_CONST;
----
<?php

\PHPUnit\Command::MAIN_CONST;

PHP
    ],
];
