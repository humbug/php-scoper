<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'two-parts class constant references in a namespace with a two-level use statement',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'two-parts' =>  <<<'PHP'
<?php

namespace A;

use X\Foo;

Foo\Bar::MAIN_CONST;
----
<?php

namespace Humbug\A;

use Humbug\X\Foo;

Foo\Bar::MAIN_CONST;

PHP
    ,

    'FQ two-parts' =>  <<<'PHP'
<?php

namespace A;

use X\Foo;

\X\Foo\Bar::MAIN_CONST;
----
<?php

namespace Humbug\A;

use Humbug\X\Foo;

\Humbug\X\Foo\Bar::MAIN_CONST;

PHP
    ,

    // If is whitelisted is made into a FQCN to avoid autoloading issues
    'whitelisted two-parts' =>  [
        'whitelist' => ['X\Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace A;

use X\Foo;

Foo\Bar::MAIN_CONST;
----
<?php

namespace Humbug\A;

use Humbug\X\Foo;

\X\Foo\Bar::MAIN_CONST;

PHP
    ],

    'FQ whitelisted two-parts' =>  [
        'whitelist' => ['X\Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace A;

use X\Foo;

\X\Foo\Bar::MAIN_CONST;
----
<?php

namespace Humbug\A;

use Humbug\X\Foo;

\X\Foo\Bar::MAIN_CONST;

PHP
    ],
];
