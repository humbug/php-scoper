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
        'title' => 'Function declarations in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Function declaration in the global namespace:
- prefix the namespace statements
- prefix the appropriate classes
SPEC
        ,
        'whitelist' => ['X\Y', 'BAR_CONST'],
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

namespace {
    const FOO_CONST = 'foo';
    const BAR_CONST = 'foo';

    function foo(
        Foo $arg0,
        \Foo $arg1,
        Foo\Bar $arg2,
        \Foo\Bar $arg3,
        ArrayIterator $arg4,
        \ArrayIterator $arg5,
        X\Y $arg6,
        \X\Y $arg7,
        string $foo = FOO_CONST,
        string $bar = BAR_CONST
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
namespace Humbug;

const FOO_CONST = 'foo';
\define('BAR_CONST', 'foo');
function foo(\Humbug\Foo $arg0, \Humbug\Foo $arg1, \Humbug\Foo\Bar $arg2, \Humbug\Foo\Bar $arg3, \ArrayIterator $arg4, \ArrayIterator $arg5, \Humbug\X\Y $arg6, \Humbug\X\Y $arg7, string $foo = \Humbug\FOO_CONST, string $bar = \BAR_CONST)
{
}

PHP
    ],

    [
        'spec' => <<<'SPEC'
Function declaration in the global namespace with use statements:
- prefix namespace statements
- prefix the appropriate classes
- append the class_alias statement to the whitelisted class
SPEC
        ,
        'whitelist' => ['X\Y'],
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

namespace {
    use Foo;
    use ArrayIterator;

    function foo(
        string $arg0,
        ?string $arg1,
        ?string $arg2 = null,
        
        Foo $arg3,
        ?Foo $arg4,
        Foo $arg5 = null,
        
        \Foo $arg6,
        ?\Foo $arg7,
        \Foo $arg8 = null,
        
        Foo\Bar $arg9,
        ?Foo\Bar $arg10,
        Foo\Bar $arg11 = null,
        
        \Foo\Bar $arg7,
        ?\Foo\Bar $arg12,
        \Foo\Bar $arg13 = null,
        
        ArrayIterator $arg14,
        ?ArrayIterator $arg15,
        ?ArrayIterator $arg16 = null,
        
        \ArrayIterator $arg17,
        ?\ArrayIterator $arg18,
        \ArrayIterator $arg19 = null,
        
        X\Y $arg20,
        \X\Y $arg21
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
namespace Humbug;

use Humbug\Foo;
use ArrayIterator;
function foo(string $arg0, ?string $arg1, ?string $arg2 = null, \Humbug\Foo $arg3, ?\Humbug\Foo $arg4, \Humbug\Foo $arg5 = null, \Humbug\Foo $arg6, ?\Humbug\Foo $arg7, \Humbug\Foo $arg8 = null, \Humbug\Foo\Bar $arg9, ?\Humbug\Foo\Bar $arg10, \Humbug\Foo\Bar $arg11 = null, \Humbug\Foo\Bar $arg7, ?\Humbug\Foo\Bar $arg12, \Humbug\Foo\Bar $arg13 = null, \ArrayIterator $arg14, ?\ArrayIterator $arg15, ?\ArrayIterator $arg16 = null, \ArrayIterator $arg17, ?\ArrayIterator $arg18, \ArrayIterator $arg19 = null, \Humbug\X\Y $arg20, \Humbug\X\Y $arg21)
{
}

PHP
    ],

    [
        'spec' => <<<'SPEC'
Function declarations with return types in the global namespace with use statements:
- prefix namespace statements
- prefix the appropriate classes
- append the class_alias statement to the whitelisted class
SPEC
        ,
        'whitelist' => ['X\Y'],
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

namespace {
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
namespace Humbug;

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
function foo() : \Humbug\X\Y
{
}
function foo() : \Humbug\X\Y
{
}
function foo() : ?\Humbug\X\Y
{
}
function foo() : ?\Humbug\X\Y
{
}

PHP
    ],
];
