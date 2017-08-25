<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Use statements for functions with group statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    <<<'PHP'
<?php

use A\{b};
use A\{B\c, d};
use \A\B\{C\d, e};

----
<?php

use Humbug\A\{b};
use Humbug\A\{B\c, d};
use Humbug\A\B\{C\d, e};

PHP
    ,
];
