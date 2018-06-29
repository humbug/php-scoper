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
        'title' => 'Static method call statement of a class imported via an aliased use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-functions' => true,
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a class belonging to the global namespace imported via an aliased use statement:
- do not touch the use statement (see tests related to the use statements of a class belonging to the global scope)
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Foo {}

use Foo as X;

X::main();
----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo as X;
\Humbug\Foo::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a class belonging to the global namespace imported via an aliased use statement:
- do not touch the use statement (see tests related to the use statements of a class belonging to the global scope)
- do not touch the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Foo {}
class X {}

use Foo as X;

\X::main();
----
<?php

namespace Humbug;

class Foo
{
}
class X
{
}
use Humbug\Foo as X;
\Humbug\X::main();

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

use Closure as X;

X::bind();
----
<?php

namespace Humbug;

use Closure as X;
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

class X {}

use Closure as X;

\X::bind();
----
<?php

namespace Humbug;

class X
{
}
use Closure as X;
\Humbug\X::bind();

PHP
    ],
];
