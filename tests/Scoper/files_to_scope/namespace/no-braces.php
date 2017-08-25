<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Namespace declarations with braces',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'one level' => <<<'PHP'
<?php

namespace Foo;

----
<?php

namespace Humbug\Foo;


PHP
    ,

    'two levels' => <<<'PHP'
<?php

namespace Foo\Bar;

----
<?php

namespace Humbug\Foo\Bar;


PHP
    ,
];
