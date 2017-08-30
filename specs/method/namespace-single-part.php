<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'single-part static method calls in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'single-part' =>  <<<'PHP'
<?php

namespace X;

Foo::main();
----
<?php

namespace Humbug\X;

Foo::main();

PHP
    ,

    'FQ single-part' =>  <<<'PHP'
<?php

namespace X;

\Foo::main();
----
<?php

namespace Humbug\X;

\Humbug\Foo::main();

PHP
    ,
];
