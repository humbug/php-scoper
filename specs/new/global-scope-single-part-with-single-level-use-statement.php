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
        'title' => 'New statement call of a class imported via a use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a class belonging to the global namespace imported via a use statement:
- prefix the namespace, use and new statements
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace {
    use Foo;
    
    new Foo();
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug;

use Humbug\Foo;
new \Humbug\Foo();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a class belonging to the global namespace imported via a use statement:
- prefix the namespace, use and new statements
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace {
    use Foo;
    
    new \Foo();
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug;

use Humbug\Foo;
new \Humbug\Foo();

PHP
    ],

    [
        'spec' => <<<'SPEC'
New statement call of an internal class:
- wrap the call in a prefixed namespace
- do not prefix the use and new statement 
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use ArrayIterator;

new ArrayIterator([]);
----
<?php

namespace Humbug;

use ArrayIterator;
new \ArrayIterator([]);

PHP
    ],
];
