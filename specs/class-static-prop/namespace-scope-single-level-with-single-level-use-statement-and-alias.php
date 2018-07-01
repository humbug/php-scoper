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
        'title' => 'Class static property call of a class imported with an aliased use statement in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-functions' => true,
    ],

    [
        'spec' => <<<'SPEC'
Constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace:
- prefix the namespace
- do not prefix the use statement (cf. class belonging to the global scope tests)
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace A {
    use Foo as X;
    
    X::$mainStaticProp;
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\A;

use Humbug\Foo as X;
\Humbug\Foo::$mainStaticProp;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace:
- prefix the namespace
- do not prefix the class (cf. class belonging to the global scope tests)
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
    class X {}
}

namespace A {
    use Foo as X;
    
    \X::$mainStaticProp;
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
\Humbug\X::$mainStaticProp;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Constant call on a whitelisted class which is imported via an aliased use statement and which belongs to the global namespace:
- prefix the namespace
- prefix the use statement (cf. class belonging to the global scope tests and `scope.inc.php` for the built-in global whitelisted classes)
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

use Reflector as X;

X::$mainStaticProp;
----
<?php

namespace Humbug\A;

use Reflector as X;
\Reflector::$mainStaticProp;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ constant call on a whitelisted class which is imported via an aliased use statement and which belongs to the global namespace:
- prefix the namespace
- prefix the use statement (cf. class belonging to the global scope tests and `scope.inc.php` for the built-in global whitelisted classes)
- do not touch the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class X {}
}

namespace A {
    use Reflector as X;
    
    \X::$mainStaticProp;
}
----
<?php

namespace Humbug;

class X
{
}
namespace Humbug\A;

use Reflector as X;
\Humbug\X::$mainStaticProp;

PHP
    ],
];
