<?php

declare(strict_types=1);

// Fixture file used to compare what the expected result is for the scoping for a given input

return [
    'meta' => [
        'title' => 'Anonymous classes',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'anonymous class declaration' =>  <<<'PHP'
<?php

new class {
    public function test() {}
};
new class extends A implements B, C {};
new class() {
    public $foo;
};
new class($a, $b) extends A {
    use T;
};

class A {
    public function test() {
        return new class($this) extends A {
            const A = 'B';
        };
    }
}
----
<?php

new class
{
    public function test()
    {
    }
};
new class extends A implements B, C
{
};
new class
{
    public $foo;
};
new class($a, $b) extends A
{
    use T;
};
class A
{
    public function test()
    {
        return new class($this) extends A
        {
            const A = 'B';
        };
    }
}

PHP
    ,

    'namespaced anonymous class declaration' =>  <<<'PHP'
<?php

namespace Foo;

new class {
    public function test() {}
};

new class extends A implements B, C {};

new class() {
    public $foo;
};

new class($a, $b) extends A {
    use T;
};

class A {
    public function test() {
        return new class($this) extends A {
            const A = 'B';
        };
    }
}
----
<?php

namespace Humbug\Foo;

new class
{
    public function test()
    {
    }
};
new class extends A implements B, C
{
};
new class
{
    public $foo;
};
new class($a, $b) extends A
{
    use T;
};
class A
{
    public function test()
    {
        return new class($this) extends A
        {
            const A = 'B';
        };
    }
}

PHP
    ,

    'multiple namespaced anonymous class declaration' =>  <<<'PHP'
<?php

namespace {
    class A {
        public function test() {
            return new class($this) extends A {
                const A = 'B';
            };
        }
    }
}

namespace Foo {
    new class {
        public function test() {}
    };
    
    new class extends A implements B, C {};
}

namespace Bar {
    new class() {
        public $foo;
    };
    
    new class($a, $b) extends A {
        use T;
    };
}

----
<?php

namespace {
    class A
    {
        public function test()
        {
            return new class($this) extends A
            {
                const A = 'B';
            };
        }
    }
}
namespace Humbug\Foo {
    new class
    {
        public function test()
        {
        }
    };
    new class extends A implements B, C
    {
    };
}
namespace Humbug\Bar {
    new class
    {
        public $foo;
    };
    new class($a, $b) extends A
    {
        use T;
    };
}

PHP
    ,
];
