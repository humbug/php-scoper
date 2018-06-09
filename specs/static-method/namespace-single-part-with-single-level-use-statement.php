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
        'title' => 'Static method call statement of a class belonging to the global namespace which has been imported with a '
                   .'use statement in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a class:
- prefix the namespace
- do not prefix the call: see tests related to the classes belonging to the global namespace
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace A {
    use Foo;
    
    Foo::main();
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\A;

use Humbug\Foo;
\Humbug\Foo::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a class:
- prefix the namespace
- do not prefix the call: see tests related to the classes belonging to the global namespace
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace A {
    use Foo;
    
    \Foo::main();
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\A;

use Humbug\Foo;
\Humbug\Foo::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a class which has been whitelisted and belongs to the global namespace:
- prefix the namespace
- prefix the call: see `scope.inc.php` for the built-in global whitelisted classes
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

use Closure;

Closure::bind();
----
<?php

namespace Humbug\A;

use Closure;
\Closure::bind();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a class which has been whitelisted and belongs to the global namespace:
- prefix the namespace
- prefix the call: see `scope.inc.php` for the built-in global whitelisted classes
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

use Closure;

\Closure::bind();
----
<?php

namespace Humbug\A;

use Closure;
\Closure::bind();

PHP
    ],
];
