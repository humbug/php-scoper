<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Final classes',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'final class declaration' =>  <<<'PHP'
<?php

final class A {}
----
<?php

final class A
{
}

PHP
    ,

    'namespaced final class declaration' =>  <<<'PHP'
<?php

namespace Foo;

final class A {}
----
<?php

namespace Humbug\Foo;

final class A
{
}

PHP
    ,
];
