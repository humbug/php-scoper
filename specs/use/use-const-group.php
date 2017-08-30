<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Use statements for consts with group statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    <<<'PHP'
<?php

use const A\{B};
use const A\{B\C, D};
use const \A\B\{C\D, E};

----
<?php

use const Humbug\A\{B};
use const Humbug\A\{B\C, D};
use const Humbug\A\B\{C\D, E};

PHP
    ,
];
