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
        'title' => 'Class static property call of a namespaced class imported with a use statement in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

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

    'Constant call on a namespaced class partially imported with a use statement' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    class Bar {}
}

namespace X {
    use Foo;
    
    Foo\Bar::$mainStaticProp;
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
namespace Humbug\X;

use Humbug\Foo;
Foo\Bar::$mainStaticProp;

PHP
    ,

    'Constant call on a namespaced class imported with a use statement' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace Foo\Bar {
    class X {}
}

namespace X {
    use Foo\Bar;
    
    Bar\X::$mainStaticProp;
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
namespace Humbug\Foo\Bar;

class X
{
}
namespace Humbug\X;

use Humbug\Foo\Bar;
Bar\X::$mainStaticProp;

PHP
    ,

    'FQ constant call on a namespaced class imported with a use statement' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    class Bar {}
}

namespace X {
    use Foo;
    
    \Foo\Bar::$mainStaticProp;
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
namespace Humbug\X;

use Humbug\Foo;
\Humbug\Foo\Bar::$mainStaticProp;

PHP
    ,

    'FQ Constant call on a whitelisted namespaced class partially imported with a use statement' => [
        'expose-classes' => ['Foo\Bar'],
        'expected-recorded-classes' => [
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

namespace X {
    use Foo;
    
    Foo\Bar::$mainStaticProp;
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
namespace Humbug\X;

use Humbug\Foo;
Foo\Bar::$mainStaticProp;

PHP
    ],

    'FQ constant call on a whitelisted namespaced class imported with a use statement' => [
        'expose-classes' => ['Foo\Bar'],
        'expected-recorded-classes' => [
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

namespace X {
    use Foo;
    
    \Foo\Bar::$mainStaticProp;
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
namespace Humbug\X;

use Humbug\Foo;
\Humbug\Foo\Bar::$mainStaticProp;

PHP
    ],
];
