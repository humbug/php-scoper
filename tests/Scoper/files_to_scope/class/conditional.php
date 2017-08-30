<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Conditional class declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'Declaration in the global namespace: do not do anything.' =>  <<<'PHP'
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

    'Declaration in a namespace: prefix each namespace.' =>  <<<'PHP'
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

    'Multiple declarations in different namespaces: prefix each namespace.' =>  <<<'PHP'
<?php

namespace X {
    if (true) {
        class A {}
    }
}

namespace Y {
    if (true) {
        class B {}
    }
}

namespace Z {
    if (true) {
        class C {}
    }
}
----
<?php

namespace Humbug\X {
    if (true) {
        class A
        {
        }
    }
}

namespace Humbug\Y {
    if (true) {
        class B
        {
        }
    }
}

namespace Humbug\Z {
    if (true) {
        class C
        {
        }
    }
}

PHP
    ,
];
