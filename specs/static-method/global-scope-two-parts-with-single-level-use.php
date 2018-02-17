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
        'title' => 'Static method call statement of a namespaced class imported with a use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a namespaced class partially imported with a use statement:
- do not touch the use statement: see tests for the use statements as to why
- prefix the call
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
    use Foo;
    
    Foo\Bar::main();
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

use Humbug\Foo;
\Humbug\Foo\Bar::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a namespaced class imported with a use statement:
- prefix the use statement
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    use Foo\Bar;
    
    Bar::main();
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
namespace Humbug;

use Humbug\Foo\Bar;
\Humbug\Foo\Bar::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a namespaced class partially imported with a use statement:
- do not touch the use statement: see tests for the use statements and classes of the global namespace as to why
- prefix the call
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
    use Foo;
    
    \Foo\Bar::main();
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

use Humbug\Foo;
\Humbug\Foo\Bar::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a namespaced class imported with a use statement:
- prefix the use statement
- do not touch the call: see tests for the use statements and classes of the global namespace as to why
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace Foo {
    class Bar {}
}

namespace {
    class Bar {}

    use Foo\Bar;
    
    \Bar::main();
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
namespace Humbug;

class Bar
{
}
use Humbug\Foo\Bar;
\Humbug\Bar::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a whitelisted namespaced class partially imported with a use statement:
- do not touch the use statement: see tests for the use statements as to why
- do not prefix the call
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
    use Foo;
    
    Foo\Bar::main();
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
class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

use Humbug\Foo;
\Humbug\Foo\Bar::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a whitelisted namespaced class imported with a use statement:
- prefix the use statement
- do not prefix the call
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
    use Foo\Bar;
    
    Bar::main();
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

use Humbug\Foo\Bar;
\Humbug\Foo\Bar::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a whitelisted namespaced class partially imported with a use statement:
- do not touch the use statement: see tests for the use statements as to why
- do not prefix the call
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
    use Foo;
    
    \Foo\Bar::main();
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
class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

use Humbug\Foo;
\Humbug\Foo\Bar::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a whitelisted namespaced class imported with a use statement:
- prefix the use statement
- do not prefix the call
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
    class Bar {}

    use Foo\Bar;
    
    \Bar::main();
}
----
<?php

namespace Humbug\Foo;

class Bar
{
}
class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

class Bar
{
}
use Humbug\Foo\Bar;
\Humbug\Bar::main();

PHP
    ],
];
