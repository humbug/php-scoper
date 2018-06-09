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
        'title' => 'New statement call of a namespaced class imported with an aliased use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a namespaced class partially imported with an aliased use statement:
- prefix the namespaces, use statement and the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    class Bar {}
}

namespace {
    use Foo as A;
    
    new A\Bar();
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

use Humbug\Foo as A;
new \Humbug\Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a namespaced class imported with an aliased use statement:
- prefix the namespaces, use statement and the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    class Bar {}
}

namespace {
    use Foo\Bar as A;
    
    new A();
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

use Humbug\Foo\Bar as A;
new \Humbug\Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a namespaced class with an aliased use statement:
- prefix the namespaces, use statement and the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace A {
    class Bar {}
}

namespace {
    use Foo as A;
    
    new \A\Bar();
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\A;

class Bar
{
}
namespace Humbug;

use Humbug\Foo as A;
new \Humbug\A\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a class with an aliased use statement:
- prefix the namespaces, use statement and the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class A {}
}

namespace Foo {
    class Bar {}
}

namespace {
    use Foo\Bar as A;
    
    new \A();
}
----
<?php

namespace Humbug;

class A
{
}
namespace Humbug\Foo;

class Bar
{
}
namespace Humbug;

use Humbug\Foo\Bar as A;
new \Humbug\A();

PHP
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a whitelisted namespaced class partially imported with an aliased use statement:
- prefix each namespace and the call
- append the class_alias statement to the whitelisted class
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    class Bar {}
}

namespace {
    use Foo as A;
    
    new A\Bar();
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

use Humbug\Foo as A;
new \Humbug\Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a whitelisted namespaced class imported with an aliased use statement:
- prefix the namespaces, use statement and the call
- append the class_alias statement to the whitelisted class
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    use Foo\Bar as A;
    
    new A();
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
\class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

use Humbug\Foo\Bar as A;
new \Humbug\Foo\Bar();

PHP
    ],
];
