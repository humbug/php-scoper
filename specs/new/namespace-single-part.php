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
        'title' => 'New statement call in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a class:
- prefix the namespace
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

class Foo {}

new Foo();
----
<?php

namespace Humbug\A;

class Foo
{
}
new \Humbug\A\Foo();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a class belonging to the global namespace:
- prefix the namespace
- do not prefix the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace A {
    new \Foo();
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\A;

new \Humbug\Foo();

PHP
    ],
];
