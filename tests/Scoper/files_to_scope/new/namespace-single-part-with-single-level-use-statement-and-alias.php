<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'single-part new statements in a namespace with single-level use statements and alias',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // See tests for the use statements as to why we don't touch the use statement.
    // Won't do anything here as this class is part of the global namespace.
    'single-part' =>  <<<'PHP'
<?php

namespace A;

use Foo as X;

new X();
----
<?php

namespace Humbug\A;

use Foo as X;

new \Foo();

PHP
    ,

    // See tests for the use statements as to why we don't touch the use statement.
    // Won't do anything here as this class is part of the global namespace.
    'FQ single-part' =>  <<<'PHP'
<?php

namespace A;

use Foo as X;

new \Foo();
----
<?php

namespace Humbug\A;

use Foo as X;

new \Foo();

PHP
    ,

    // See tests for the use statements as to why we don't touch the use statement.
    // See `scope.inc.php` for the built-in global whitelisted classes
    '(global) whitelisted single-part' =>  <<<'PHP'
<?php

namespace A;

use AppKernel as X;

new X();
----
<?php

namespace Humbug\A;

use Humbug\AppKernel as X;

new X();

PHP
    ,

    // See tests for the use statements as to why we don't touch the use statement.
    // See `scope.inc.php` for the built-in global whitelisted classes
    '(global) whitelisted FQ single-part' =>  <<<'PHP'
<?php

namespace A;

use AppKernel as X;

new \X();
----
<?php

namespace Humbug\A;

use Humbug\AppKernel as X;

new \Humbug\AppKernel();

PHP
    ,
];
