<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'global function call in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // We don't do anything as there is no ways to distinguish between a namespaced function call
    // from the same namespace and a function registered in the global scope
    'single-part' =>  <<<'PHP'
<?php

namespace X;

main();
----
<?php

namespace Humbug\X;

main();

PHP
    ,

    'FQ single-part' =>  <<<'PHP'
<?php

namespace X;

\main();
----
<?php

namespace Humbug\X;

\main();

PHP
    ,
];
