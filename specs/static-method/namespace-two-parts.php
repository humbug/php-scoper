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
        'title' => 'Static method call statement of a namespaced class in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],

        'expose-global-constants' => true,
        'expose-global-classes' => false,
        'expose-global-functions' => true,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Static method call statement of a class' => <<<'PHP'
<?php

namespace X\Foo {
    class Bar {}
}

namespace X {
    Foo\Bar::main();
}
----
<?php

namespace Humbug\X\Foo;

class Bar
{
}
namespace Humbug\X;

Foo\Bar::main();

PHP
    ,

    'FQ static method call statement of a class' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace X {
    \Foo\Bar::main();
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
namespace Humbug\X;

\Humbug\Foo\Bar::main();

PHP
    ,

    'Static method call statement of a whitelisted class' => [
        'whitelist' => ['X\Foo\Bar'],
        'registered-classes' => [
            ['X\Foo\Bar', 'Humbug\X\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
<?php

namespace X\Foo {
    class Bar {}
}

namespace X {
    Foo\Bar::main();
}
----
<?php

namespace Humbug\X\Foo;

class Bar
{
}
\class_alias('Humbug\\X\\Foo\\Bar', 'X\\Foo\\Bar', \false);
namespace Humbug\X;

Foo\Bar::main();

PHP
    ],

    'FQ static method call statement of a non-whitelisted class' => [
        'whitelist' => ['X\Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace X {
    \Foo\Bar::main();
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
namespace Humbug\X;

\Humbug\Foo\Bar::main();

PHP
    ],
];
