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
        'title' => 'Class constant call of a class imported with an aliased use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],

        'expose-global-constants' => true,
        'expose-global-classes' => false,
        'expose-global-functions' => true,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'expected-recorded-classes' => [],
        'expected-recorded-functions' => [],
    ],

    'Constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace' => [
        'payload' => <<<'PHP'
<?php

class Foo {}

use Foo as X;

X::MAIN_CONST;
----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo as X;
X::MAIN_CONST;

PHP
    ],

    'FQ constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace' => [
        'payload' => <<<'PHP'
<?php

class Foo {}
class X {}

use Foo as X;

\X::MAIN_CONST;
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
\Humbug\X::MAIN_CONST;

PHP
    ],

    'Constant call on a whitelisted class which is imported via an aliased use statement and which belongs to the global namespace' => [
        'payload' => <<<'PHP'
<?php

use Reflector as X;

X::MAIN_CONST;
----
<?php

namespace Humbug;

use Reflector as X;
X::MAIN_CONST;

PHP
    ],

    'FQ constant call on a whitelisted class which is imported via an aliased use statement and which belongs to the global namespace' => [
        'payload' => <<<'PHP'
<?php

class X {}

use Reflector as X;

\X::MAIN_CONST;
----
<?php

namespace Humbug;

class X
{
}
use Reflector as X;
\Humbug\X::MAIN_CONST;

PHP
    ],
];
