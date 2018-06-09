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
        'title' => 'Class declaration with an extend',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
    ],

    'Declaration in the global namespace: prefix only non-internal classes.' => <<<'PHP'
<?php

class A {
    public function a() {}
}

class B extends A implements Iterator {
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
class B extends \Humbug\A implements \Iterator
{
}

PHP
    ,

    'Declaration in a namespace: prefix the namespace.' => <<<'PHP'
<?php

namespace Foo;

use Iterator;

class A {
    public function a() {}
}

class B extends A implements Iterator {
}
----
<?php

namespace Humbug\Foo;

use Iterator;
class A
{
    public function a()
    {
    }
}
class B extends \Humbug\Foo\A implements \Iterator
{
}

PHP
    ,

    'Declaration of a whitelisted class: append aliasing.' => [
        'whitelist' => ['Foo\B'],
        'payload' => <<<'PHP'
<?php

namespace Foo;

class A {
    public function a() {}
}

class B extends A {
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
class B extends \Humbug\Foo\A
{
}
\class_alias('Humbug\\Foo\\B', 'Foo\\B', \false);

PHP
        ],

    'Declaration in a different namespace imported via a use statement: prefix the namespace.' => <<<'PHP'
<?php

namespace Foo;

class A {
    public function a() {}
}

namespace Bar;

use Foo\A;

class B extends A {
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

use Humbug\Foo\A;
class B extends \Humbug\Foo\A
{
}

PHP
    ,

    'Declaration in a different namespace imported via a FQ call: prefix the namespace.' => <<<'PHP'
<?php

namespace Foo;

class A {
    public function a() {}
}

namespace Bar;

class B extends \Foo\A {
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

class B extends \Humbug\Foo\A
{
}

PHP
    ,
];
