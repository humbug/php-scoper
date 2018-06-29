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
        'title' => 'Interface declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-functions' => true,
    ],

    'Declaration in the global namespace: add a prefixed namespace.' => <<<'PHP'
<?php

class C {}
class D {}

interface A extends C, D, Iterator {
    public function a();
}
----
<?php

namespace Humbug;

class C
{
}
class D
{
}
interface A extends \Humbug\C, \Humbug\D, \Iterator
{
    public function a();
}

PHP
    ,

    'Declaration in a namespace: prefix the namespace.' => <<<'PHP'
<?php

namespace Foo;

use Iterator;

class C {}
class D {}

interface A extends C, D, Iterator
{
    public function a();
}
----
<?php

namespace Humbug\Foo;

use Iterator;
class C
{
}
class D
{
}
interface A extends \Humbug\Foo\C, \Humbug\Foo\D, \Iterator
{
    public function a();
}

PHP
    ,

    'Declaration of a whitelisted interface: append aliasing.' => [
        'whitelist' => ['Foo\A'],
        'payload' => <<<'PHP'
<?php

namespace Foo;

use Iterator;

class C {}
class D {}

interface A extends C, D, Iterator
{
    public function a();
}
----
<?php

namespace Humbug\Foo;

use Iterator;
class C
{
}
class D
{
}
interface A extends \Humbug\Foo\C, \Humbug\Foo\D, \Iterator
{
    public function a();
}
\class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);

PHP
        ],

    'Multiple declarations in different namespaces: prefix each namespace.' => <<<'PHP'
<?php

namespace X {
    class D {}
    class E {}

    interface A extends D, E
    {
        public function a();
    }
}

namespace Y {
    class D {}
    class E {}

    interface B extends D, E
    {
        public function a();
    }
}

namespace Z {
    class D {}
    class E {}

    interface C extends D, E
    {
        public function a();
    }
}
----
<?php

namespace Humbug\X;

class D
{
}
class E
{
}
interface A extends \Humbug\X\D, \Humbug\X\E
{
    public function a();
}
namespace Humbug\Y;

class D
{
}
class E
{
}
interface B extends \Humbug\Y\D, \Humbug\Y\E
{
    public function a();
}
namespace Humbug\Z;

class D
{
}
class E
{
}
interface C extends \Humbug\Z\D, \Humbug\Z\E
{
    public function a();
}

PHP
    ,
];
