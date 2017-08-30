<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'global constant reference in a namespace with single-level use statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'single-part' =>  <<<'PHP'
<?php

namespace X;

use constant DUMMY_CONST;

DUMMY_CONST;
----
<?php

namespace Humbug\X;

use constant Humbug\DUMMY_CONST;

DUMMY_CONST;

PHP
    ,

    'FQ single-part' =>  <<<'PHP'
<?php

namespace X;

use constant DUMMY_CONST;

\DUMMY_CONST;
----
<?php

namespace Humbug\X;

use constant Humbug\DUMMY_CONST;

\DUMMY_CONST;

PHP
    ,
];
