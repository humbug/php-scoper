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
        'title' => 'Abstract class declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-functions' => true,
    ],

    'Declaration in the global namespace: add prefixed namespace' => <<<'PHP'
<?php

abstract class A {
    public function a() {}
    abstract public function b();
}
----
<?php

namespace Humbug;

abstract class A
{
    public function a()
    {
    }
    public abstract function b();
}

PHP
    ,

    'Declaration in the global namespace with the global namespace whitelisted: add root namespace statement' => [
        'whitelist' => ['\*'],
        'payload' => <<<'PHP'
<?php

abstract class A {
    public function a() {}
    abstract public function b();
}
----
<?php

namespace {
    abstract class A
    {
        public function a()
        {
        }
        public abstract function b();
    }
}

PHP
    ],

    [
        'spec' => <<<'SPEC'
Declaration of a whitelisted class in the global namespace:
- add prefixed namespace
- append class alias statement to the class declaration
SPEC
        ,
        'whitelist' => ['A'],
        'payload' => <<<'PHP'
<?php

abstract class A {
    public function a() {}
    abstract public function b();
}
----
<?php

namespace Humbug;

abstract class A
{
    public function a()
    {
    }
    public abstract function b();
}
\class_alias('Humbug\\A', 'A', \false);

PHP
    ],

    'Declaration of a whitelisted class in the global namespace which is whitelisted: add empty namespace statement' => [
        'whitelist' => ['A', '\*'],
        'payload' => <<<'PHP'
<?php

abstract class A {
    public function a() {}
    abstract public function b();
}
----
<?php

namespace {
    abstract class A
    {
        public function a()
        {
        }
        public abstract function b();
    }
}

PHP
    ],

    'Declaration in a namespace: prefix the namespace' => <<<'PHP'
<?php

namespace Foo;

abstract class A {
    public function a() {}
    abstract public function b();
}
----
<?php

namespace Humbug\Foo;

abstract class A
{
    public function a()
    {
    }
    public abstract function b();
}

PHP
    ,

    [
        'spec' => <<<'SPEC'
Declaration of a whitelisted class in the global namespace:
- add prefixed namespace
- append class alias statement to the class declaration
SPEC
        ,
        'whitelist' => ['A'],
        'payload' => <<<'PHP'
<?php

abstract class A {
    public function a() {}
    abstract public function b();
}
----
<?php

namespace Humbug;

abstract class A
{
    public function a()
    {
    }
    public abstract function b();
}
\class_alias('Humbug\\A', 'A', \false);

PHP
    ],

    'Declaration in a whitelisted namespace: do nothing' => [
        'whitelist' => ['Foo\*'],
        'payload' => <<<'PHP'
<?php

namespace Foo;

abstract class A {
    public function a() {}
    abstract public function b();
}
----
<?php

namespace Foo;

abstract class A
{
    public function a()
    {
    }
    public abstract function b();
}

PHP
    ],

    'Declaration of a whitelisted class: append aliasing' => [
        'whitelist' => ['Foo\A'],
        'payload' => <<<'PHP'
<?php

namespace Foo;

abstract class A {
    public function a() {}
    abstract public function b();
}
----
<?php

namespace Humbug\Foo;

abstract class A
{
    public function a()
    {
    }
    public abstract function b();
}
\class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);

PHP
    ],

    'Declaration of a whitelisted class with FQCN for the whitelist: append aliasing' => [
        'whitelist' => ['\Foo\A'],
        'payload' => <<<'PHP'
<?php

namespace Foo;

abstract class A {
    public function a() {}
    abstract public function b();
}
----
<?php

namespace Humbug\Foo;

abstract class A
{
    public function a()
    {
    }
    public abstract function b();
}
\class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);

PHP
    ],

    'Declaration of a class belonging to a whitelisted namespace' => [
        'whitelist' => ['\*'],
        'payload' => <<<'PHP'
<?php

namespace Foo;

abstract class A {
    public function a() {}
    abstract public function b();
}
----
<?php

namespace Foo;

abstract class A
{
    public function a()
    {
    }
    public abstract function b();
}

PHP
    ],

    'Multiple declarations in different namespaces with whitelisted classes: prefix each namespace' => [
        'whitelist' => ['Foo\WA', 'Bar\WB', 'WC'],
        'payload' => <<<'PHP'
<?php

namespace Foo {

    abstract class A {
        public function a() {}
    }

    abstract class WA {
        public function a() {}
    }
}

namespace Bar {

    abstract class B {
        public function b() {}
    }

    abstract class WB {
        public function b() {}
    }
}

namespace {

    abstract class C {
        public function c() {}
    }

    abstract class WC {
        public function c() {}
    }
}
----
<?php

namespace Humbug\Foo;

abstract class A
{
    public function a()
    {
    }
}
abstract class WA
{
    public function a()
    {
    }
}
\class_alias('Humbug\\Foo\\WA', 'Foo\\WA', \false);
namespace Humbug\Bar;

abstract class B
{
    public function b()
    {
    }
}
abstract class WB
{
    public function b()
    {
    }
}
\class_alias('Humbug\\Bar\\WB', 'Bar\\WB', \false);
namespace Humbug;

abstract class C
{
    public function c()
    {
    }
}
abstract class WC
{
    public function c()
    {
    }
}
\class_alias('Humbug\\WC', 'WC', \false);

PHP
    ],
];
