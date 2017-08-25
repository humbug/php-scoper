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

    'empty random file' => [
        'input' => <<<'PHP'

PHP
        ,
        'output' => <<<'PHP'


PHP
    ],

    'empty php file' => [
        'input' => <<<'PHP'
<?php

PHP
        ,
        'output' => <<<'PHP'
<?php


PHP
    ],

    'empty php file with a declare statement' => [
        'input' => <<<'PHP'
<?php declare(strict_types=1);

PHP
        ,
        'output' => <<<'PHP'
<?php declare(strict_types=1);


PHP
    ],
];
