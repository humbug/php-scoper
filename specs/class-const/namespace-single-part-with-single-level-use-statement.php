<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'single-part class constant references in a namespace with single-level use statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // See tests for the use statements as to why we don't touch the use statement.
    // Won't do anything here as this class is part of the global namespace.
    'single-part' =>  <<<'PHP'
<?php

namespace X;

use Foo;

Foo::MAIN_CONST;
----
<?php

namespace Humbug\X;

use Foo;

\Foo::MAIN_CONST;

PHP
    ,

    // See tests for the use statements as to why we don't touch the use statement.
    // Won't do anything here as this class is part of the global namespace.
    'FQ single-part' =>  <<<'PHP'
<?php

namespace X;

use Foo;

\Foo::MAIN_CONST;
----
<?php

namespace Humbug\X;

use Foo;

\Foo::MAIN_CONST;

PHP
    ,

    // See tests for the use statements as to why we don't touch the use statement.
    // See `scope.inc.php` for the built-in global whitelisted classes
    '(global) whitelisted single-part' =>  <<<'PHP'
<?php

namespace X;

use AppKernel;

AppKernel::MAIN_CONST;
----
<?php

namespace Humbug\X;

use Humbug\AppKernel;

AppKernel::MAIN_CONST;

PHP
    ,

    // See tests for the use statements as to why we don't touch the use statement.
    // See `scope.inc.php` for the built-in global whitelisted classes
    '(global) whitelisted FQ single-part' =>  <<<'PHP'
<?php

namespace X;

use AppKernel;

\AppKernel::MAIN_CONST;
----
<?php

namespace Humbug\X;

use Humbug\AppKernel;

\Humbug\AppKernel::MAIN_CONST;

PHP
    ,
];
