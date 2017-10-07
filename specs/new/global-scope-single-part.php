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
        'title' => 'New statement call in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a class belonging to the global namespace:
- wrap everything in a prefixed namespace
- prefix the class
- transform the class in a FQCN call
prefix it
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Foo {}

new Foo();
----
<?php

namespace Humbug;

class Foo
{
}
new \Humbug\Foo();

PHP
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a class belonging to the global namespace:
- wrap everything in a prefixed namespace
- do not prefix the class
- transform the class in a FQCN call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

new ArrayIterator([]);
----
<?php

namespace Humbug;

new \ArrayIterator([]);

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a class belonging to the global namespace:
- wrap everything in a prefixed namespace
- prefix the class
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Foo {}

new \Foo();
----
<?php

namespace Humbug;

class Foo
{
}
new \Humbug\Foo();

PHP
    ],
];
