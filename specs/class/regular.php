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
        'title' => 'Class declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Declaration in the global namespace' => <<<'PHP'
<?php

class A {
    public function a() {}
}
----
<?php

namespace Humbug;

class A
{
    public function a()
    {
    }
}

PHP
    ,

    'Declaration in the global namespace with global classes whitelisted' => [
        'whitelist-global-classes' => true,
        'registered-classes' => [
            ['A', 'Humbug\A'],
        ],
        'payload' => <<<'PHP'
<?php

class A {
    public function a() {}
}
----
<?php

namespace Humbug;

class A
{
    public function a()
    {
    }
}
\class_alias('Humbug\\A', 'A', \false);

PHP
    ],

    'Declaration in a namespace' => <<<'PHP'
<?php

namespace Foo;

class A {
    public function a() {}
}
----
<?php

namespace Humbug\Foo;

class A
{
    public function a()
    {
    }
}

PHP
    ,

    'Declaration in a namespace with global classes whitelisted' => [
        'whitelist-global-classes' => true,
        'payload' => <<<'PHP'
<?php

namespace Foo;

class A {
    public function a() {}
}
----
<?php

namespace Humbug\Foo;

class A
{
    public function a()
    {
    }
}

PHP
    ],

    'Declaration of a whitelisted class' => [
        'whitelist' => ['Foo\A'],
        'registered-classes' => [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        'payload' => <<<'PHP'
<?php

namespace Foo;

class A {
    public function a() {}
}
----
<?php

namespace Humbug\Foo;

class A
{
    public function a()
    {
    }
}
\class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);

PHP
    ],

    'Declaration of a whitelisted class whitelisted with a pattern' => [
        'whitelist' => ['Foo\A*'],
        'registered-classes' => [
            ['Foo\A', 'Humbug\Foo\A'],
            ['Foo\AA', 'Humbug\Foo\AA'],
            ['Foo\A\B', 'Humbug\Foo\A\B'],
        ],
        'payload' => <<<'PHP'
<?php

namespace Foo;

class A {
    public function a() {}
}

class AA {}

class B {}

namespace Foo\A;

class B {}

----
<?php

namespace Humbug\Foo;

class A
{
    public function a()
    {
    }
}
\class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);
class AA
{
}
\class_alias('Humbug\\Foo\\AA', 'Foo\\AA', \false);
class B
{
}
namespace Humbug\Foo\A;

class B
{
}
\class_alias('Humbug\\Foo\\A\\B', 'Foo\\A\\B', \false);

PHP
    ],

    'Multiple declarations in different namespaces' => <<<'PHP'
<?php

namespace Foo {

    class A {
        public function a() {}
    }
}

namespace Bar {

    class B {
        public function b() {}
    }
}

namespace {

    class C {
        public function c() {}
    }
}
----
<?php

namespace Humbug\Foo;

class A
{
    public function a()
    {
    }
}
namespace Humbug\Bar;

class B
{
    public function b()
    {
    }
}
namespace Humbug;

class C
{
    public function c()
    {
    }
}

PHP
    ,

    'Multiple declarations in different namespaces with whitelisted class' => [
        'whitelist' => [
            'Foo\A',
            'Bar\B',
        ],
        'registered-classes' => [
            ['Foo\A', 'Humbug\Foo\A'],
            ['Bar\B', 'Humbug\Bar\B'],
        ],
        'payload' => <<<'PHP'
<?php

namespace Foo {

    class A {
        public function a() {}
    }
    
    class B {
        public function b() {}
    }
    
    class C {
        public function c() {}
    }
}

namespace Bar {

    class A {
        public function a() {}
    }
    
    class B {
        public function b() {}
    }
    
    class C {
        public function c() {}
    }
}

namespace {

    class A {
        public function a() {}
    }
    
    class B {
        public function b() {}
    }
    
    class C {
        public function c() {}
    }
}
----
<?php

namespace Humbug\Foo;

class A
{
    public function a()
    {
    }
}
\class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);
class B
{
    public function b()
    {
    }
}
class C
{
    public function c()
    {
    }
}
namespace Humbug\Bar;

class A
{
    public function a()
    {
    }
}
class B
{
    public function b()
    {
    }
}
\class_alias('Humbug\\Bar\\B', 'Bar\\B', \false);
class C
{
    public function c()
    {
    }
}
namespace Humbug;

class A
{
    public function a()
    {
    }
}
class B
{
    public function b()
    {
    }
}
class C
{
    public function c()
    {
    }
}

PHP
        ],
];
