<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Mixed use statements with group statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    <<<'PHP'
<?php

use A\B\{C\D, function b\c, const D};

----
<?php

use Humbug\A\B\{C\D, function b\c, const D};

PHP
    ,
];
