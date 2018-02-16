<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'meta' => [
        'title' => 'Conditional class declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'Declaration in the global namespace: warp in a prefixed namespace.' => <<<'PHP'
<?php

if (true) {
    class A {}
}
----
<?php

namespace Humbug;

if (\true) {
    class A
    {
    }
}

PHP
    ,

    [
        'spec' => <<<'SPEC'
Declaration of a whitelisted class in the global namespace: warp in a prefixed namespace.

TODO: unsupported at the moment. The `class_alias` statement appended to support whitelisted classes are added at the
end of a namespace statement for now. This could be supported if they are added right after the declaration statement
instead. 
SPEC
        ,
        'whitelist' => ['A'],
        'payload' => <<<'PHP'
<?php

if (true) {
    class A {}
}
----
<?php

namespace Humbug;

if (\true) {
    class A
    {
    }
}

PHP
    ,

    'Declaration in a namespace: prefix each namespace.' => <<<'PHP'
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
    ],

    'Declaration of a whitelisted class: prefix the namespace, too dynamic to account for.' => [
        'whitelist' => ['Foo\A'],
        'payload' => <<<'PHP'
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
        ],

    'Multiple declarations in different namespaces: prefix each namespace.' => <<<'PHP'
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

namespace Humbug\X;

if (true) {
    class A
    {
    }
}
namespace Humbug\Y;

if (true) {
    class B
    {
    }
}
namespace Humbug\Z;

if (true) {
    class C
    {
    }
}

PHP
    ,
];
