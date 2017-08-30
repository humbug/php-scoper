<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Miscellaneous',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'empty PHP file' =>  <<<'PHP'
<?php

----
<?php



PHP
    ,

    'empty php file with a declare statement' =>  <<<'PHP'
<?php declare(strict_types=1);

----
<?php

declare (strict_types=1);

PHP
    ,
];
