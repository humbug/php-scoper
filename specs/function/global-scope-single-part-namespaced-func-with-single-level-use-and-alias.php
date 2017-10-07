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
        'title' => 'Namespaced function call imported with an aliased use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Namespaced function call imported with a partial use statement in the global scope
- do not prefix the use statement: see tests related to classes from the global namespace
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    function main() {}
}

namespace {
    use Foo as X;
    
    X\main();
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\Foo;

function main()
{
}
namespace Humbug;

use Humbug\Foo as X;
\Humbug\Foo\main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ namespaced function call imported with a partial use statement in the global scope
- do not prefix the use statement: see tests related to classes from the global namespace
- prefix the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace X {
    function main() {}
}

namespace {
    use Foo as X;
    
    \X\main();
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\X;

function main()
{
}
namespace Humbug;

use Humbug\Foo as X;
\Humbug\X\main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Whitelisted namespaced function call imported with a partial use statement in the global scope
- do not prefix the use statement: see tests related to classes from the global namespace
- prefix the call: whitelists only works on classes
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['Foo\main'],
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    function main() {}
}

namespace {
    use Foo as X;
    
    X\main();
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\Foo;

function main()
{
}
namespace Humbug;

use Humbug\Foo as X;
\Humbug\Foo\main();

PHP
    ],
];
