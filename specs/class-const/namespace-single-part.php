<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'single-part class constant references in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'single-part' =>  <<<'PHP'
<?php

namespace X;

Foo::MAIN_CONST;
----
<?php

namespace Humbug\X;

Foo::MAIN_CONST;

PHP
    ,

    'FQ single-part' =>  <<<'PHP'
<?php

namespace X;

\Foo::MAIN_CONST;
----
<?php

namespace Humbug\X;

\Humbug\Foo::MAIN_CONST;

PHP
    ,
];
