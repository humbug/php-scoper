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
        'title' => 'New statement call of a class imported via an aliased use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-functions' => true,
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a class belonging to the global namespace imported via an aliased use statement:
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
    use Foo as X;
    
    new X();
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug;

use Humbug\Foo as X;
new \Humbug\Foo();

PHP
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a class belonging to the global namespace imported via an aliased use statement; the call is made
using the original class instead of the alias
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
    use Foo as X;
    
    new Foo();
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug;

use Humbug\Foo as X;
new \Humbug\Foo();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a class belonging to the global namespace imported via an aliased use statement:
- prefix the namespace, use and new statements
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
    class X {}
}

namespace {
    use Foo as X;
    
    new \X();
}
----
<?php

namespace Humbug;

class Foo
{
}
class X
{
}
namespace Humbug;

use Humbug\Foo as X;
new \Humbug\X();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a class belonging to the global namespace imported via an aliased use statement; the new
statement uses the class directly instead of the alias
- prefix the namespace, use and new statements
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
    class X {}
}

namespace {
    use Foo as X;
    
    new \X();
}
----
<?php

namespace Humbug;

class Foo
{
}
class X
{
}
namespace Humbug;

use Humbug\Foo as X;
new \Humbug\X();

PHP
    ],

    [
        'spec' => <<<'SPEC'
New statement call of an internal class imported with an aliased use statement:
- wrap the call in a prefixed namespace
- do not prefix the use and new statement 
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use ArrayIterator as X;

new X([]);
----
<?php

namespace Humbug;

use ArrayIterator as X;
new \ArrayIterator([]);

PHP
    ],
];
