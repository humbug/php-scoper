<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Conditional classes',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'conditional class declaration' =>  <<<'PHP'
<?php

if (true) {
    class A {}
}
----
<?php

if (true) {
    class A
    {
    }
}

PHP
    ,

    'namespaced abstract class declaration' =>  <<<'PHP'
<?php

namespace Foo;

if (true) {
    class A {}
}
----
<?php

namespace Humbug\Foo;

if (true) {
    class A
    {
    }
}

PHP
    ,
];
