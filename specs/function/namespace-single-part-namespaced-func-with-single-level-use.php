<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'single-part namespaced function call in a namespace with a single-level use statement',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // See tests for the use statements as to why we don't touch the use statement.
    // The use statement cannot be prefixed, but as the class is in two-parts this
    // is enough to know we can prefix it.
    'two-parts' =>  <<<'PHP'
<?php

namespace X;

use Foo;

Foo\main();
----
<?php

namespace Humbug\X;

use Foo;

\Humbug\Foo\main();

PHP
    ,

    // See tests for the use statements as to why we don't touch the use statement.
    // As there is nothing in PHP core with more than two-parts, we can safely prefix.
    'FQ two-parts' =>  <<<'PHP'
<?php

namespace X;

use Foo;

\Foo\main();
----
<?php

namespace Humbug\X;

use Foo;

\Humbug\Foo\main();

PHP
    ,

    // See tests for the use statements as to why we don't touch the use statement.
    // Whitelisting a function call has no effect
    'whitelisted two-parts' =>  [
        'whitelist' => ['X\Foo\main'],
        'payload' => <<<'PHP'
<?php

namespace X;

use Foo;

Foo\main();
----
<?php

namespace Humbug\X;

use Foo;

\Humbug\Foo\main();

PHP
    ],

    // See tests for the use statements as to why we don't touch the use statement.
    // Whitelisting a function call has no effect
    'FQ whitelisted two-parts' =>  [
        'whitelist' => ['X\Foo\main'],
        'payload' => <<<'PHcP'
<?php

namespace X;

use Foo;

\Foo\main();
----
<?php

namespace Humbug\X;

use Foo;

\Humbug\Foo\main();

PHP
    ],
];
