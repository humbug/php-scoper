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
        'title' => 'Static method call statement of a class imported via a use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a class belonging to the global namespace imported via a use statement:
- do not touch the use statement (see tests related to the use statements of a class belonging to the global scope)
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Foo {}

use Foo;

Foo::main();
----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo;
\Humbug\Foo::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a class belonging to the global namespace imported via a use statement:
- do not touch the use statement (see tests related to the use statements of a class belonging to the global scope)
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Foo {}

use Foo;

\Foo::main();
----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo;
\Humbug\Foo::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a class belonging to the global namespace which has been whitelisted:
- prefix the use statement
- prefix the call
- transform the call into a FQ call
- See `scope.inc.php` for the built-in global whitelisted classes
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Closure;

Closure::bind();
----
<?php

namespace Humbug;

use Closure;
\Closure::bind();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a class belonging to the global namespace which has been whitelisted:
- prefix the statement
- prefix the call
- See `scope.inc.php` for the built-in global whitelisted classes
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Closure;

\Closure::bind();
----
<?php

namespace Humbug;

use Closure;
\Closure::bind();

PHP
    ],
];
