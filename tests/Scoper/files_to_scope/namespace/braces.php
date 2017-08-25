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

    'root namespace' => <<<'PHP'
<?php

namespace {
    $x = 'root';
}

----
<?php

namespace {
    $x = 'root';
}

PHP
    ,

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

    'already prefixed namespace - one level' => <<<'PHP'
<?php

namespace Humbug\Foo {
}

----
<?php

namespace Humbug\Foo;


PHP
    ,

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

    'already prefixed — two levels' => <<<'PHP'
<?php

namespace Humbug\Foo\Bar;

----
<?php

namespace Humbug\Foo\Bar;


PHP
    ,

    'mix' => <<<'PHP'
<?php

// single level
namespace A {
    $x = 'a';
}

// single level
namespace B {
    $x = 'b';
}

// already prefixed one level
namespace Humbug\C {
    $x = 'pa';
}

// two levels
namespace D\E {
    $x = 'de';
}

// two levels
namespace F\G {
    $x = 'fg';
}

// already prefixed two levels
namespace Humbug\H\I {
    $x = 'phi';
}

// root namespace
namespace {
    $x = 'root';
}

----
<?php

// single level
namespace Humbug\A {
    $x = 'a';
}
// single level
namespace Humbug\B {
    $x = 'b';
}
// already prefixed one level
namespace Humbug\C {
    $x = 'pa';
}
// two levels
namespace Humbug\D\E {
    $x = 'de';
}
// two levels
namespace Humbug\F\G {
    $x = 'fg';
}
// already prefixed two levels
namespace Humbug\H\I {
    $x = 'phi';
}
// root namespace
namespace {
    $x = 'root';
}

PHP
    ,
];
