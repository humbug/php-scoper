<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Namespace declarations without braces',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'one level' => <<<'PHP'
<?php

namespace Foo {
}

----
<?php

namespace Humbug\Foo;


PHP
    ,

    'whitelisted namespace - one level' => [
        // Does nothing as the whitelist is effective only on a class
        'whitelist' => [
            'Foo',
        ],
        'payload' => <<<'PHP'
<?php

namespace Foo {
}

----
<?php

namespace Humbug\Foo;


PHP
    ],

    'two levels' => <<<'PHP'
<?php

namespace Foo\Bar {
}

----
<?php

namespace Humbug\Foo\Bar;


PHP
    ,

    'whitelisted namespace - two levels' => [
        // Does nothing as the whitelist is effective only on a class
        'whitelist' => [
            'Foo',
        ],
        'payload' => <<<'PHP'
<?php

namespace Foo\Bar {
}

----
<?php

namespace Humbug\Foo\Bar;


PHP
    ],

    'multiple one level statement' => <<<'PHP'
<?php

namespace Foo {
    $x = 'foo';
}

namespace Bar {
    $x = 'bar';
}

----
<?php

namespace Humbug\Foo;

$x = 'foo';
namespace Humbug\Bar;

$x = 'bar';

PHP
    ,
];
