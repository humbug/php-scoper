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

use function A\{b};
use function A\{B\c, d};
use function \A\B\{C\d, e};

----
<?php

use function Humbug\A\{b};
use function Humbug\A\{B\c, d};
use function Humbug\A\B\{C\d, e};

PHP
    ,
];
