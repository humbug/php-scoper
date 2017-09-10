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
    ],

    'Declaration in the global namespace: do not do anything.' => <<<'PHP'
<?php

class A {
    public function a() {}
}

class B extends A {
}
----
<?php

class A
{
    public function a()
    {
    }
}
class B extends \A
{
}

PHP
    ,

    'Declaration in a namespace: prefix the namespace.' => <<<'PHP'
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

PHP
    ,

    'Declaration of a namespaced whitelisted class: do not prefix the namespace.' => [
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
