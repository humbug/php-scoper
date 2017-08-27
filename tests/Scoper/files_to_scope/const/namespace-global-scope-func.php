<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'global constant reference in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // We don't do anything as there is no ways to distinguish between a namespaced constant reference
    // from the same namespace and a function registered in the global scope
    'single-part' =>  <<<'PHP'
<?php

namespace X;

DUMMY_CONST;
----
<?php

namespace Humbug\X;

DUMMY_CONST;

PHP
    ,

    'FQ single-part' =>  <<<'PHP'
<?php

namespace X;

\DUMMY_CONST;
----
<?php

namespace Humbug\X;

\DUMMY_CONST;

PHP
    ,
];
