<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'global function call in a namespace with single-level use statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'single-part' =>  <<<'PHP'
<?php

namespace X;

use function main;

main();
----
<?php

namespace Humbug\X;

use function Humbug\main;

main();

PHP
    ,

    'FQ single-part' =>  <<<'PHP'
<?php

namespace X;

use function main;

\main();
----
<?php

namespace Humbug\X;

use function Humbug\main;

\main();

PHP
    ,
];
