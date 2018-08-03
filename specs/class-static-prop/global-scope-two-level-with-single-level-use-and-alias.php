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
        'title' => 'Class static property call of a namespaced class imported with an aliased use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Constant call on a namespaced class partially imported with an aliased use statement' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    class Bar {}
}

namespace {
    use Foo as X;
    
    X\Bar::$mainStaticProp;
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\Foo;

class Bar
{
}
namespace Humbug;

use Humbug\Foo as X;
\Humbug\Foo\Bar::$mainStaticProp;

PHP
    ,

    'Constant call on a namespaced class imported with an aliased use statement' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    use Foo\Bar as X;
    
    X::$mainStaticProp;
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
namespace Humbug;

use Humbug\Foo\Bar as X;
\Humbug\Foo\Bar::$mainStaticProp;

PHP
    ,

    'FQ constant call on a namespaced class imported with an aliased use statement' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace X {
    class Bar {}
}

namespace {
    use Foo as X;
    
    \X\Bar::$mainStaticProp;
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\X;

class Bar
{
}
namespace Humbug;

use Humbug\Foo as X;
\Humbug\X\Bar::$mainStaticProp;

PHP
    ,

    'FQ Constant call on a whitelisted namespaced class partially imported with an aliased use statement' => [
        'whitelist' => ['Foo\Bar'],
        'registered-classes' => [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    class Bar {}
}

namespace {
    use Foo as X;
    
    X\Bar::$mainStaticProp;
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\Foo;

class Bar
{
}
\class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

use Humbug\Foo as X;
\Humbug\Foo\Bar::$mainStaticProp;

PHP
    ],

    'FQ constant call on a whitelisted namespaced class imported with an aliased use statement' => [
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace X {
    class Bar {}
}

namespace {
    use Foo as X;
    
    \X\Bar::$mainStaticProp;
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\X;

class Bar
{
}
namespace Humbug;

use Humbug\Foo as X;
\Humbug\X\Bar::$mainStaticProp;

PHP
    ],
];
