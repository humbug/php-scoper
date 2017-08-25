<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Namespaces with an outside statement',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'declare statement' => <<<'PHP'
<?php

declare(A='B');

namespace Foo;

----
<?php

declare (A='B');
namespace Humbug\Foo;


PHP
    ,

    'comment' => <<<'PHP'
<?php

/* Comment */

namespace Foo;

----
<?php

/* Comment */
namespace Humbug\Foo;


PHP
    ,
];
