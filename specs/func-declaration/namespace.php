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
        'title' => 'Function declarations in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Simple function declaration' => <<<'PHP'
<?php

namespace Acme;

function foo() {}

----
<?php

namespace Humbug\Acme;

function foo()
{
}

PHP
    ,

    'Simple whitelisted function' => [
        'whitelist' => ['Acme\foo'],
        'registered-functions' => [
            ['Acme\foo', 'Humbug\Acme\foo'],
        ],
        'payload' => <<<'PHP'
<?php

namespace Acme;

function foo() {}

----
<?php

namespace Humbug\Acme;

function foo()
{
}

PHP
    ],

    'Function declaration in a namespace' => [
        'whitelist' => ['X\Y'],
        'registered-classes' => [
            ['X\Y', 'Humbug\X\Y'],
        ],
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    class Bar {}
}

namespace X {
    class Y {}
}

namespace Pi\Foo {
    class Bar {}
}

namespace Pi\X {
    class Y {}
}

namespace Pi {
    class Foo {}
    class ArrayIterator {}

    function foo(
        Foo $arg0 = null,
        \Foo $arg1,
        Foo\Bar $arg2,
        \Foo\Bar $arg3,
        ArrayIterator $arg4,
        \ArrayIterator $arg5,
        X\Y $arg6,
        \X\Y $arg7
    ) {}
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\Foo;

class Bar
{
}
namespace Humbug\X;

class Y
{
}
\class_alias('Humbug\\X\\Y', 'X\\Y', \false);
namespace Humbug\Pi\Foo;

class Bar
{
}
namespace Humbug\Pi\X;

class Y
{
}
namespace Humbug\Pi;

class Foo
{
}
class ArrayIterator
{
}
function foo(\Humbug\Pi\Foo $arg0 = null, \Humbug\Foo $arg1, \Humbug\Pi\Foo\Bar $arg2, \Humbug\Foo\Bar $arg3, \Humbug\Pi\ArrayIterator $arg4, \ArrayIterator $arg5, \Humbug\Pi\X\Y $arg6, \Humbug\X\Y $arg7)
{
}

PHP
    ],

    'Function declaration in a namespace with whitelisted classes' => [
        'whitelist' => ['X\Y'],
        'registered-classes' => [
            ['X\Y', 'Humbug\X\Y'],
        ],
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    class Bar {}
}

namespace X {
    class Y {}
}

namespace Pi\Foo {
    class Bar {}
}

namespace Pi\X {
    class Y {}
}

namespace Pi {
    class Foo {}
    class ArrayIterator {}

    function foo(
        Foo $arg0 = null,
        \Foo $arg1,
        Foo\Bar $arg2,
        \Foo\Bar $arg3,
        ArrayIterator $arg4,
        \ArrayIterator $arg5,
        X\Y $arg6,
        \X\Y $arg7
    ) {}
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\Foo;

class Bar
{
}
namespace Humbug\X;

class Y
{
}
\class_alias('Humbug\\X\\Y', 'X\\Y', \false);
namespace Humbug\Pi\Foo;

class Bar
{
}
namespace Humbug\Pi\X;

class Y
{
}
namespace Humbug\Pi;

class Foo
{
}
class ArrayIterator
{
}
function foo(\Humbug\Pi\Foo $arg0 = null, \Humbug\Foo $arg1, \Humbug\Pi\Foo\Bar $arg2, \Humbug\Foo\Bar $arg3, \Humbug\Pi\ArrayIterator $arg4, \ArrayIterator $arg5, \Humbug\Pi\X\Y $arg6, \Humbug\X\Y $arg7)
{
}

PHP
    ],

    'Function declaration in a namespace with use statements' => [
        'whitelist' => ['X\Y'],
        'registered-classes' => [
            ['X\Y', 'Humbug\X\Y'],
        ],
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    class Bar {}
}

namespace X {
    class Y {}
}

namespace Pi\X {
    class Y {}
}

namespace Pi {
    use Foo;
    use Foo\Bar;
    use ArrayIterator;
    use X\Y;

    function foo(
        Foo $arg0,
        \Foo $arg1,
        Foo\Bar $arg2,
        \Foo\Bar $arg3,
        ArrayIterator $arg4,
        \ArrayIterator $arg5,
        X\Y $arg6,
        \X\Y $arg7
    ) {}
}

----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\Foo;

class Bar
{
}
namespace Humbug\X;

class Y
{
}
\class_alias('Humbug\\X\\Y', 'X\\Y', \false);
namespace Humbug\Pi\X;

class Y
{
}
namespace Humbug\Pi;

use Humbug\Foo;
use Humbug\Foo\Bar;
use ArrayIterator;
use Humbug\X\Y;
function foo(\Humbug\Foo $arg0, \Humbug\Foo $arg1, \Humbug\Foo\Bar $arg2, \Humbug\Foo\Bar $arg3, \ArrayIterator $arg4, \ArrayIterator $arg5, \Humbug\Pi\X\Y $arg6, \Humbug\X\Y $arg7)
{
}

PHP
    ],

    'Function declaration in a whitelisted namespace' => [
        'whitelist' => ['Pi\*'],
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    class Bar {}
}

namespace X {
    class Y {}
}

namespace Pi\Foo {
    class Bar {}
}

namespace Pi\X {
    class Y {}
}

namespace Pi {
    class Foo {}
    class ArrayIterator {}

    function foo(
        Foo $arg0 = null,
        \Foo $arg1,
        Foo\Bar $arg2,
        \Foo\Bar $arg3,
        ArrayIterator $arg4,
        \ArrayIterator $arg5,
        X\Y $arg6,
        \X\Y $arg7
    ) {}
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\Foo;

class Bar
{
}
namespace Humbug\X;

class Y
{
}
namespace Pi\Foo;

class Bar
{
}
namespace Pi\X;

class Y
{
}
namespace Pi;

class Foo
{
}
class ArrayIterator
{
}
function foo(\Pi\Foo $arg0 = null, \Humbug\Foo $arg1, \Pi\Foo\Bar $arg2, \Humbug\Foo\Bar $arg3, \Pi\ArrayIterator $arg4, \ArrayIterator $arg5, \Pi\X\Y $arg6, \Humbug\X\Y $arg7)
{
}

PHP
    ],

    'Function declarations with return types in a namespace with use statements' => [
        'whitelist' => ['X\Y'],
        'registered-classes' => [
            ['X\Y', 'Humbug\X\Y'],
        ],
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    class Bar {}
}

namespace X {
    class Y {}
}

namespace Pi {
    use Foo;
    use ArrayIterator;
    
    function foo(): void {}
    
    function foo(): bool {}
    function foo(): ?bool {}
    
    function foo(): int {}
    function foo(): ?int {}
    
    function foo(): float {}
    function foo(): ?float {}
    
    function foo(): string {}
    function foo(): ?string {}
    
    function foo(): array {}
    function foo(): ?array {}
    
    function foo(): iterable {}
    function foo(): ?iterable {}
    
    function foo(): callable {}
    function foo(): ?callable {}

    function foo(): Foo {}
    function foo(): \Foo {}
    function foo(): ?Foo {}
    function foo(): ?\Foo {}

    function foo(): ArrayIterator {}
    function foo(): \ArrayIterator {}
    function foo(): ?ArrayIterator {}
    function foo(): ?\ArrayIterator {}
    
    function foo(): X\Y {}
    function foo(): \X\Y {}
    function foo(): ?X\Y {}
    function foo(): ?\X\Y {}
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\Foo;

class Bar
{
}
namespace Humbug\X;

class Y
{
}
\class_alias('Humbug\\X\\Y', 'X\\Y', \false);
namespace Humbug\Pi;

use Humbug\Foo;
use ArrayIterator;
function foo() : void
{
}
function foo() : bool
{
}
function foo() : ?bool
{
}
function foo() : int
{
}
function foo() : ?int
{
}
function foo() : float
{
}
function foo() : ?float
{
}
function foo() : string
{
}
function foo() : ?string
{
}
function foo() : array
{
}
function foo() : ?array
{
}
function foo() : iterable
{
}
function foo() : ?iterable
{
}
function foo() : callable
{
}
function foo() : ?callable
{
}
function foo() : \Humbug\Foo
{
}
function foo() : \Humbug\Foo
{
}
function foo() : ?\Humbug\Foo
{
}
function foo() : ?\Humbug\Foo
{
}
function foo() : \ArrayIterator
{
}
function foo() : \ArrayIterator
{
}
function foo() : ?\ArrayIterator
{
}
function foo() : ?\ArrayIterator
{
}
function foo() : \Humbug\Pi\X\Y
{
}
function foo() : \Humbug\X\Y
{
}
function foo() : ?\Humbug\Pi\X\Y
{
}
function foo() : ?\Humbug\X\Y
{
}

PHP
    ],
];
