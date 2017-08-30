<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'single-part new statements in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'single-part' =>  <<<'PHP'
<?php

namespace X;

new Foo();
----
<?php

namespace Humbug\X;

new Foo();

PHP
    ,

    'FQ single-part' =>  <<<'PHP'
<?php

namespace X;

new \Foo();
----
<?php

namespace Humbug\X;

new \Humbug\Foo();

PHP
    ,
];
