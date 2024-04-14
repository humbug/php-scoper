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

use Humbug\PhpScoper\Scoper\Spec\Meta;
use Humbug\PhpScoper\Scoper\Spec\SpecWithConfig;

return [
    'meta' => new Meta(
        title: 'Method declarations',
    ),

    'Method declarations' => SpecWithConfig::create(
        exposeClasses: ['X\Y', 'BAR_CONST'],
        expectedRecordedClasses: [
            ['X\Y', 'Humbug\X\Y'],
        ],
        spec: <<<'PHP'
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
                class Main {
                    const FOO_CONST = 'foo';
                    const BAR_CONST = 'bar';

                    function foo(
                        Foo $arg0,
                        \Foo $arg1,
                        Foo\Bar $arg2,
                        \Foo\Bar $arg3,
                        ArrayIterator $arg4,
                        \ArrayIterator $arg5,
                        X\Y $arg6,
                        \X\Y $arg7,
                        string $foo = self::FOO_CONST,
                        string $bar = self::BAR_CONST
                    ) {}

                    static function foo(
                        Foo $arg0,
                        \Foo $arg1,
                        Foo\Bar $arg2,
                        \Foo\Bar $arg3,
                        ArrayIterator $arg4,
                        \ArrayIterator $arg5,
                        X\Y $arg6,
                        \X\Y $arg7,
                        string $foo = self::FOO_CONST,
                        string $bar = self::BAR_CONST
                    ) {}
                }
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

            class Main
            {
                const FOO_CONST = 'foo';
                const BAR_CONST = 'bar';
                function foo(Foo $arg0, \Humbug\Foo $arg1, Foo\Bar $arg2, \Humbug\Foo\Bar $arg3, \ArrayIterator $arg4, \ArrayIterator $arg5, \Humbug\X\Y $arg6, \Humbug\X\Y $arg7, string $foo = self::FOO_CONST, string $bar = self::BAR_CONST)
                {
                }
                static function foo(Foo $arg0, \Humbug\Foo $arg1, Foo\Bar $arg2, \Humbug\Foo\Bar $arg3, \ArrayIterator $arg4, \ArrayIterator $arg5, \Humbug\X\Y $arg6, \Humbug\X\Y $arg7, string $foo = self::FOO_CONST, string $bar = self::BAR_CONST)
                {
                }
            }

            PHP,
    ),

    'Method declarations with return types' => SpecWithConfig::create(
        exposeClasses: ['X\Y'],
        expectedRecordedClasses: [
            ['X\Y', 'Humbug\X\Y'],
        ],
        spec: <<<'PHP'
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

                class Main {
                    function foo(): self {}
                    function foo(): void {}
                    function foo(): static {}
                    function foo(): never {}

                    function foo(): false {}
                    function foo(): true {}
                    function foo(): null {}

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
            class Main
            {
                function foo() : self
                {
                }
                function foo() : void
                {
                }
                function foo() : static
                {
                }
                function foo() : never
                {
                }
                function foo() : false
                {
                }
                function foo() : true
                {
                }
                function foo() : null
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
                function foo() : Foo
                {
                }
                function foo() : \Humbug\Foo
                {
                }
                function foo() : ?Foo
                {
                }
                function foo() : ?\Humbug\Foo
                {
                }
                function foo() : ArrayIterator
                {
                }
                function foo() : \ArrayIterator
                {
                }
                function foo() : ?ArrayIterator
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
            }

            PHP,
    ),
];
