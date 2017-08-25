<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Abstract classes',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'abstract class declaration' =>  <<<'PHP'
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

    'namespaced abstract class declaration' =>  <<<'PHP'
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

    'multiple namespaced abstract class declaration' =>  <<<'PHP'
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
