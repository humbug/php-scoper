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

use Humbug\PhpScoper\SpecFramework\Config\Meta;
use Humbug\PhpScoper\SpecFramework\Config\SpecWithConfig;

return [
    'meta' => new Meta(
        title: 'Class name resolution',
    ),

    'Different kind of exposed class constant calls in the global scope' => SpecWithConfig::create(
        exposeClasses: ['Foo\Bar', 'Foo\Bar\Poz'],
        expectedRecordedClasses: [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
            ['Foo\Bar\Poz', 'Humbug\Foo\Bar\Poz'],
        ],
        spec: <<<'PHP'
            <?php

            namespace {

                class Foo {}
            }

            namespace Foo {

                class Bar {}
            }

            namespace Foo\Bar {

                class Poz {}
            }

            namespace {
                use Foo as X;
                use Foo\Bar as Y;
                use Foo\Bar\Poz as Z;

                Foo::MAIN_CONST;
                X::MAIN_CONST;

                Y::MAIN_CONST;
                X\Bar::MAIN_CONST;
                Foo\Bar::MAIN_CONST;

                Z::MAIN_CONST;
                Y\Poz::MAIN_CONST;
                X\Bar\Poz::MAIN_CONST;
                Foo\Bar\Poz::MAIN_CONST;
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
            \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
            namespace Humbug\Foo\Bar;

            class Poz
            {
            }
            \class_alias('Humbug\\Foo\\Bar\\Poz', 'Foo\\Bar\\Poz', \false);
            namespace Humbug;

            use Humbug\Foo as X;
            use Humbug\Foo\Bar as Y;
            use Humbug\Foo\Bar\Poz as Z;
            Foo::MAIN_CONST;
            X::MAIN_CONST;
            Y::MAIN_CONST;
            X\Bar::MAIN_CONST;
            \Humbug\Foo\Bar::MAIN_CONST;
            Z::MAIN_CONST;
            Y\Poz::MAIN_CONST;
            X\Bar\Poz::MAIN_CONST;
            \Humbug\Foo\Bar\Poz::MAIN_CONST;

            PHP,
    ),

    'Different kind of class constant calls in the global scope' => <<<'PHP'
        <?php

        namespace {

            class Foo {}
        }

        namespace Foo {

            class Bar {}
        }

        namespace Foo\Bar {

            class Poz {}
        }

        namespace {
            use Foo as X;
            use Foo\Bar as Y;
            use Foo\Bar\Poz as Z;

            Foo::MAIN_CONST;
            X::MAIN_CONST;

            Y::MAIN_CONST;
            X\Bar::MAIN_CONST;
            Foo\Bar::MAIN_CONST;

            Z::MAIN_CONST;
            Y\Poz::MAIN_CONST;
            X\Bar\Poz::MAIN_CONST;
            Foo\Bar\Poz::MAIN_CONST;
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
        namespace Humbug\Foo\Bar;

        class Poz
        {
        }
        namespace Humbug;

        use Humbug\Foo as X;
        use Humbug\Foo\Bar as Y;
        use Humbug\Foo\Bar\Poz as Z;
        Foo::MAIN_CONST;
        X::MAIN_CONST;
        Y::MAIN_CONST;
        X\Bar::MAIN_CONST;
        Foo\Bar::MAIN_CONST;
        Z::MAIN_CONST;
        Y\Poz::MAIN_CONST;
        X\Bar\Poz::MAIN_CONST;
        Foo\Bar\Poz::MAIN_CONST;

        PHP,

    'Different kind of class constant calls in a namespace' => SpecWithConfig::create(
        exposeClasses: [
            'Foo\Bar',
            'Foo\Bar\Poz',

            'A\Foo',
            'A\Foo\Bar',
            'A\Foo\Bar\Poz',
            'A\Aoo',
            'A\Aoo\Aoz',
            'A\Aoz',
            'A\Aoo\Aoz\Poz',
        ],
        expectedRecordedClasses: [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
            ['Foo\Bar\Poz', 'Humbug\Foo\Bar\Poz'],

            ['A\Foo', 'Humbug\A\Foo'],
            ['A\Foo\Bar', 'Humbug\A\Foo\Bar'],
            ['A\Foo\Bar\Poz', 'Humbug\A\Foo\Bar\Poz'],
            ['A\Aoo', 'Humbug\A\Aoo'],
            ['A\Aoo\Aoz', 'Humbug\A\Aoo\Aoz'],
            ['A\Aoo\Aoz\Poz', 'Humbug\A\Aoo\Aoz\Poz'],
        ],
        spec: <<<'PHP'
            <?php

            namespace {

                class Foo {}
            }

            namespace Foo {

                class Bar {}
            }

            namespace Foo\Bar {

                class Poz {}
            }

            namespace A {

                class Aoo {}
                class Foo {}
            }

            namespace A\Foo {

                class Bar {}
            }

            namespace A\Foo\Bar {

                class Poz {}
            }

            namespace A\Aoo {
                class Aoz {}
            }

            namespace A\Aoo\Aoz {
                class Poz {}
            }

            namespace A {

                use Foo as X;
                use Foo\Bar as Y;
                use Foo\Bar\Poz as Z;

                Aoo::MAIN_CONST;
                Aoo\Aoz::MAIN_CONST;
                Aoo\Aoz\Poz::MAIN_CONST;

                Foo::MAIN_CONST;
                X::MAIN_CONST;

                Y::MAIN_CONST;
                X\Bar::MAIN_CONST;
                Foo\Bar::MAIN_CONST;

                Z::MAIN_CONST;
                Y\Poz::MAIN_CONST;
                X\Bar\Poz::MAIN_CONST;
                Foo\Bar\Poz::MAIN_CONST;
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
            \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
            namespace Humbug\Foo\Bar;

            class Poz
            {
            }
            \class_alias('Humbug\\Foo\\Bar\\Poz', 'Foo\\Bar\\Poz', \false);
            namespace Humbug\A;

            class Aoo
            {
            }
            \class_alias('Humbug\\A\\Aoo', 'A\\Aoo', \false);
            class Foo
            {
            }
            \class_alias('Humbug\\A\\Foo', 'A\\Foo', \false);
            namespace Humbug\A\Foo;

            class Bar
            {
            }
            \class_alias('Humbug\\A\\Foo\\Bar', 'A\\Foo\\Bar', \false);
            namespace Humbug\A\Foo\Bar;

            class Poz
            {
            }
            \class_alias('Humbug\\A\\Foo\\Bar\\Poz', 'A\\Foo\\Bar\\Poz', \false);
            namespace Humbug\A\Aoo;

            class Aoz
            {
            }
            \class_alias('Humbug\\A\\Aoo\\Aoz', 'A\\Aoo\\Aoz', \false);
            namespace Humbug\A\Aoo\Aoz;

            class Poz
            {
            }
            \class_alias('Humbug\\A\\Aoo\\Aoz\\Poz', 'A\\Aoo\\Aoz\\Poz', \false);
            namespace Humbug\A;

            use Humbug\Foo as X;
            use Humbug\Foo\Bar as Y;
            use Humbug\Foo\Bar\Poz as Z;
            Aoo::MAIN_CONST;
            Aoo\Aoz::MAIN_CONST;
            Aoo\Aoz\Poz::MAIN_CONST;
            Foo::MAIN_CONST;
            X::MAIN_CONST;
            Y::MAIN_CONST;
            X\Bar::MAIN_CONST;
            Foo\Bar::MAIN_CONST;
            Z::MAIN_CONST;
            Y\Poz::MAIN_CONST;
            X\Bar\Poz::MAIN_CONST;
            Foo\Bar\Poz::MAIN_CONST;

            PHP,
    ),
];
