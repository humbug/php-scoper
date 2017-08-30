<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Abstract class declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'Declaration in the global namespace: do not do anything.' =>  <<<'PHP'
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

    'Declaration in a namespace: prefix the namespace.' =>  <<<'PHP'
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

    'Multiple declarations in different namespaces: prefix each namespace.' =>  <<<'PHP'
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
