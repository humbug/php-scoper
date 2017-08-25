<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Use statements for classes with group use statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'grouped statements' => <<<'PHP'
<?php

use A\{B};
use A\{B\C, D};
use \A\B\{C\D, E};

----
<?php

use Humbug\A\{B};
use Humbug\A\{B\C, D};
use Humbug\A\B\{C\D, E};

PHP
    ,

    'already prefixed grouped statements' => <<<'PHP'
<?php

use Humbug\A\{B};
use Humbug\A\{B\C, D};
use \Humbug\A\B\{C\D, E};

----
<?php

use Humbug\A\{B};
use Humbug\A\{B\C, D};
use Humbug\A\B\{C\D, E};

PHP
    ,

    // The use statement is still prefixed as usual. The usages of that statement
    // will however be transformed into FQC
    'grouped statements with a whitelisted class' => [
        'whitelist' => [
            'A\B',
            'A\B\C',
        ],
        'payload' => <<<'PHP'
<?php

use A\{B};
use A\{B\C, D};
use \A\B\{C\D, E};

----
<?php

use Humbug\A\{B};
use Humbug\A\{B\C, D};
use Humbug\A\B\{C\D, E};

PHP
    ],
];
