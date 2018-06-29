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
        'whitelist-global-functions' => true,
    ],

    'Declaration in the global namespace: add a prefixed namespace.' => <<<'PHP'
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

    'Declaration in a namespace: prefix the namespace.' => <<<'PHP'
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

    'Declaration of a whitelisted class: append aliasing.' => [
        'whitelist' => ['Foo\A'],
        'payload' => <<<'PHP'
<?php

namespace Humbug\Foo;

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

    'Multiple declarations in different namespaces: prefix each namespace.' => <<<'PHP'
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

    'Multiple declarations in different namespaces with whitelisted class: prefix namespaces and append aliasing' => [
        'whitelist' => [
            'Foo\A',
            'Bar\B',
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
