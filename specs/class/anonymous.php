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
        'title' => 'Anonymous class declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'Declaration in the global namespace: do not do anything.' => <<<'PHP'
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

    'Declaration in a namespace: prefix the namespace.' => <<<'PHP'
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

    'Multiple declarations in different namespaces: prefix each namespace.' => <<<'PHP'
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
