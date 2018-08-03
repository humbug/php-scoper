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
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Static method call statement of a namespaced class partially imported with a use statement' => <<<'PHP'
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
    ,

    'Static method call statement of a namespaced class imported with a use statement' => <<<'PHP'
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
    ,

    'FQ static method call statement of a namespaced class partially imported with a use statement' => <<<'PHP'
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
    ,

    'FQ static method call statement of a namespaced class imported with a use statement' => <<<'PHP'
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
    ,

    'Static method call statement of a whitelisted namespaced class partially imported with a use statement' => [
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
\class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

use Humbug\Foo;
\Humbug\Foo\Bar::main();

PHP
    ],

    'Static method call statement of a whitelisted namespaced class imported with a use statement' => [
        'whitelist' => ['Foo\Bar'],
        'registered-classes' => [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
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
\class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

use Humbug\Foo\Bar;
\Humbug\Foo\Bar::main();

PHP
    ],

    'FQ static method call statement of a whitelisted namespaced class partially imported with a use statement' => [
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
\class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

use Humbug\Foo;
\Humbug\Foo\Bar::main();

PHP
    ],

    'FQ static method call statement of a whitelisted namespaced class imported with a use statement' => [
        'whitelist' => ['Foo\Bar'],
        'registered-classes' => [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
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
\class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
namespace Humbug;

class Bar
{
}
use Humbug\Foo\Bar;
\Humbug\Bar::main();

PHP
    ],
];
