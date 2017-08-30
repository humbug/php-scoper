<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'two-parts static method calls in a namespace with a two-level use statement',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'two-parts' =>  <<<'PHP'
<?php

namespace A;

use X\Foo;

Foo\Bar::main();
----
<?php

namespace Humbug\A;

use Humbug\X\Foo;

Foo\Bar::main();

PHP
    ,

    'FQ two-parts' =>  <<<'PHP'
<?php

namespace A;

use X\Foo;

\X\Foo\Bar::main();
----
<?php

namespace Humbug\A;

use Humbug\X\Foo;

\Humbug\X\Foo\Bar::main();

PHP
    ,

    // If is whitelisted is made into a FQCN to avoid autoloading issues
    'whitelisted two-parts' =>  [
        'whitelist' => ['X\Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace A;

use X\Foo;

Foo\Bar::main();
----
<?php

namespace Humbug\A;

use Humbug\X\Foo;

\X\Foo\Bar::main();

PHP
    ],

    'FQ whitelisted two-parts' =>  [
        'whitelist' => ['X\Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace A;

use X\Foo;

\X\Foo\Bar::main();
----
<?php

namespace Humbug\A;

use Humbug\X\Foo;

\X\Foo\Bar::main();

PHP
    ],
];
