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
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Static method call statement of a class belonging to the global namespace imported via an aliased use statement' => <<<'PHP'
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
    ,

    'FQ static method call statement of a class belonging to the global namespace imported via an aliased use statement' => <<<'PHP'
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
    ,

    'Static method call statement of a class belonging to the global namespace which has been whitelisted' => <<<'PHP'
<?php

use Closure as X;

X::bind();
----
<?php

namespace Humbug;

use Closure as X;
\Closure::bind();

PHP
    ,

    'FQ static method call statement of a class belonging to the global namespace which has been whitelisted' => <<<'PHP'
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
    ,
];
