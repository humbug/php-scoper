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

    'Declaration in the global namespace: prefix non-internal classes.' => <<<'PHP'
<?php

interface B {}
interface C {}

new class {
    public function test() {}
};
new class extends A implements B, C, Iterator {};
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

namespace Humbug;

interface B
{
}
interface C
{
}
new class
{
    public function test()
    {
    }
};
new class extends \Humbug\A implements \Humbug\B, \Humbug\C, \Iterator
{
};
new class
{
    public $foo;
};
new class($a, $b) extends \Humbug\A
{
    use T;
};
class A
{
    public function test()
    {
        return new class($this) extends \Humbug\A
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

interface B {}
interface C {}

new class {
    public function test() {}
};

new class extends A implements B, C, \Iterator {};

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

interface B
{
}
interface C
{
}
new class
{
    public function test()
    {
    }
};
new class extends \Humbug\Foo\A implements \Humbug\Foo\B, \Humbug\Foo\C, \Iterator
{
};
new class
{
    public $foo;
};
new class($a, $b) extends \Humbug\Foo\A
{
    use T;
};
class A
{
    public function test()
    {
        return new class($this) extends \Humbug\Foo\A
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
    use A;

    interface B {}
    interface C {}

    new class {
        public function test() {}
    };

    new class extends A implements B, C, \Iterator {};
}

namespace Bar {
    use A;

    new class() {
        public $foo;
    };

    new class($a, $b) extends A {
        use T;
    };
}

----
<?php

namespace Humbug;

class A
{
    public function test()
    {
        return new class($this) extends \Humbug\A
        {
            const A = 'B';
        };
    }
}
namespace Humbug\Foo;

use Humbug\A;
interface B
{
}
interface C
{
}
new class
{
    public function test()
    {
    }
};
new class extends \Humbug\A implements \Humbug\Foo\B, \Humbug\Foo\C, \Iterator
{
};
namespace Humbug\Bar;

use Humbug\A;
new class
{
    public $foo;
};
new class($a, $b) extends \Humbug\A
{
    use T;
};

PHP
    ,
];
