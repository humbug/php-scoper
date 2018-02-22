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
        'title' => 'Use statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Use statement of a class belonging to the global scope:
- wrap the statement in a prefixed namespace
- prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Foo {}

use Foo;

----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ use statement of a class belonging to the global scope:
- wrap the statement in a prefixed namespace
- prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Foo {}

use \Foo;

----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement of an internal class belonging to the global scope:
- wrap the statement in a prefixed namespace
- do not prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use ArrayIterator;

----
<?php

namespace Humbug;

use ArrayIterator;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement of an internal class belonging to the global scope:
- wrap the statement in a prefixed namespace
- do not prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use \ArrayIterator;

----
<?php

namespace Humbug;

use ArrayIterator;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement of a non existent class belonging to the global scope:
- wrap the statement in a prefixed namespace
- do not prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Unknown;

----
<?php

namespace Humbug;

use Humbug\Unknown;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement of two-level class:
- prefix the namespaces
- prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    use Foo\Bar;
}

----
<?php

namespace Humbug\Foo;

class Bar
{
}
namespace Humbug;

use Humbug\Foo\Bar;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Already prefixed use statement of two-level class:
- prefix the namespaces
- prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    use Humbug\Foo\Bar;
}

----
<?php

namespace Humbug\Foo;

class Bar
{
}
namespace Humbug;

use Humbug\Foo\Bar;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement of two-level class which has been whitelisted:
- prefix the namespaces
- append the class_alias statement to the whitelisted class
- do not prefix the use statement
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    use Foo\Bar;
}

----
<?php

namespace Humbug\Foo;

class Bar
{
}
\class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

use Humbug\Foo\Bar;

PHP
    ],
];
