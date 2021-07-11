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
        'title' => 'Class constant call of a class imported with an aliased use statement in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'excluded-namespaces' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'excluded-constants' => [],
        'excluded-classes' => [],
        'excluded-functions' => [],
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace' => [
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace A {
    use Foo as X;
    
    X::MAIN_CONST;
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\A;

use Humbug\Foo as X;
X::MAIN_CONST;

PHP
    ],

    'FQ constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace' => [
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
    class X {}
}

namespace A {
    use Foo as X;
    
    \X::MAIN_CONST;
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
namespace Humbug\A;

use Humbug\Foo as X;
\Humbug\X::MAIN_CONST;

PHP
    ],

    'Constant call on a whitelisted class which is imported via an aliased use statement and which belongs to the global namespace' => [
        'payload' => <<<'PHP'
<?php

namespace A;

use Reflector as X;

X::MAIN_CONST;
----
<?php

namespace Humbug\A;

use Reflector as X;
X::MAIN_CONST;

PHP
    ],

    'FQ constant call on a whitelisted class which is imported via an aliased use statement and which belongs to the global namespace' => [
        'payload' => <<<'PHP'
<?php

namespace {
    class X {}
}

namespace A {
    use Reflector as X;
    
    \X::MAIN_CONST;
}
----
<?php

namespace Humbug;

class X
{
}
namespace Humbug\A;

use Reflector as X;
\Humbug\X::MAIN_CONST;

PHP
    ],
];
