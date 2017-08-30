<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'single-part namespaced function call in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'two-parts' =>  <<<'PHP'
<?php

namespace X;

PHPUnit\main();
----
<?php

namespace Humbug\X;

PHPUnit\main();

PHP
    ,

    'FQ two-parts' =>  <<<'PHP'
<?php

namespace X;

\PHPUnit\main();
----
<?php

namespace Humbug\X;

\Humbug\PHPUnit\main();

PHP
    ,

    // Whitelisting a function has no effect
    'whitelisted two-parts' =>  [
        'whitelist' => ['X\PHPUnit\main'],
        'payload' => <<<'PHP'
<?php

namespace X;

PHPUnit\main();
----
<?php

namespace Humbug\X;

PHPUnit\main();

PHP
    ],

    'FQ whitelisted two-parts' =>  [
        'whitelist' => ['PHPUnit\main'],
        'payload' => <<<'PHP'
<?php

namespace X;

\PHPUnit\main();
----
<?php

namespace Humbug\X;

\Humbug\PHPUnit\main();

PHP
    ],
];
