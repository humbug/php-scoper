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
        'whitelist-global-constants' => true,
        'whitelist-global-functions' => true,
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a class:
- prefix the namespace
- prefix the call
- transform the call into a FQ call
SPEC
        ,
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
namespace Humbug\X;

\Humbug\X\Foo\Bar::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a class:
- prefix the namespace
- prefix the call
- transform the call into a FQ call
SPEC
        ,
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

    [
        'spec' => <<<'SPEC'
Static method call statement of a whitelisted class:
- prefix the namespace
- do not prefix the call
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['X\Foo\Bar'],
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

\Humbug\X\Foo\Bar::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a non-whitelisted class:
- prefix the namespace
- prefix the call
SPEC
        ,
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
