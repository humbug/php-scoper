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
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Declaration in the global namespace' => <<<'PHP'
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

    'Declaration in the global namespace with global classes whitelisted' => [
        'whitelist-global-classes' => true,
        'registered-classes' => [
            ['A', 'Humbug\A'],
            ['B', 'Humbug\B'],
            ['C', 'Humbug\C'],
        ],
        'payload' => <<<'PHP'
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
\class_alias('Humbug\\B', 'B', \false);
interface C
{
}
\class_alias('Humbug\\C', 'C', \false);
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
\class_alias('Humbug\\A', 'A', \false);

PHP
        ,
    ],

    'Declaration in the global namespace which is whitelisted' => [
        'whitelist' => ['\*'],
        'payload' => <<<'PHP'
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

namespace {
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
    new class extends \A implements \B, \C, \Iterator
    {
    };
    new class
    {
        public $foo;
    };
    new class($a, $b) extends \A
    {
        use T;
    };
    class A
    {
        public function test()
        {
            return new class($this) extends \A
            {
                const A = 'B';
            };
        }
    }
}

PHP
    ],

    'Declaration in the global namespace with some whitelisted classes' => [
        'whitelist' => ['A', 'C'],
        'registered-classes' => [
            ['A', 'Humbug\A'],
            ['C', 'Humbug\C'],
        ],
        'payload' => <<<'PHP'
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
\class_alias('Humbug\\C', 'C', \false);
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
\class_alias('Humbug\\A', 'A', \false);

PHP
    ],

    'Declaration in a namespace' => <<<'PHP'
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

    'Multiple declarations in different namespaces' => <<<'PHP'
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
