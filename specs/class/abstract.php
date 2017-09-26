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
    ],

    'Declaration in the global namespace: do not do anything.' => <<<'PHP'
<?php

abstract class A {
    public function a() {}
    abstract public function b();
}
----
<?php

abstract class A
{
    public function a()
    {
    }
    public abstract function b();
}

PHP
    ,

    'Declaration in a namespace: prefix the namespace.' => <<<'PHP'
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

    'Declaration of a whitelisted namespaced class: append aliasing.' => [
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
class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);

PHP
        ],

    'Multiple declarations in different namespaces: prefix each namespace.' => <<<'PHP'
<?php

namespace Foo {

    abstract class A {
        public function a() {}
    }
}

namespace Bar {

    abstract class B {
        public function b() {}
    }
}

namespace {

    abstract class C {
        public function c() {}
    }
}
----
<?php

namespace Humbug\Foo {
    abstract class A
    {
        public function a()
        {
        }
    }
}
namespace Humbug\Bar {
    abstract class B
    {
        public function b()
        {
        }
    }
}
namespace {
    abstract class C
    {
        public function c()
        {
        }
    }
}

PHP
    ,
];
