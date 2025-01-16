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
        title: 'Class declaration',
    ),

    'Declaration in the global namespace' => <<<'PHP'
        <?php

        class A {
            public function a() {}
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

        PHP,

    'Declaration in the global namespace with global classes exposed' => SpecWithConfig::create(
        exposeGlobalClasses: true,
        expectedRecordedClasses: [
            ['A', 'Humbug\A'],
        ],
        spec: <<<'PHP'
            <?php

            class A {
                public function a() {}
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
            \class_alias('Humbug\A', 'A', \false);

            PHP,
    ),

    'Declaration in the global namespace with global classes exposed within a condition' => SpecWithConfig::create(
        exposeGlobalClasses: true,
        expectedRecordedClasses: [
            ['A', 'Humbug\A'],
        ],
        spec: <<<'PHP'
            <?php

            if ($condition) {
                class A {
                    public function a() {}
                }
            }
            ----
            <?php

            namespace Humbug;

            if ($condition) {
                class A
                {
                    public function a()
                    {
                    }
                }
                \class_alias('Humbug\A', 'A', \false);
            }

            PHP,
    ),

    'Declaration of an internal class' => SpecWithConfig::create(
        expectedRecordedClasses: [
            ['Normalizer', 'Humbug\Normalizer'],
        ],
        spec: <<<'PHP'
            <?php

            class Normalizer {}
            ----
            <?php

            namespace Humbug;

            class Normalizer
            {
            }
            \class_alias('Humbug\Normalizer', 'Normalizer', \false);

            PHP,
    ),

    'Declaration in a namespace' => <<<'PHP'
        <?php

        namespace Foo;

        class A {
            public function a() {}
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

        PHP,

    'Declaration in a namespace with global classes exposed' => SpecWithConfig::create(
        exposeGlobalClasses: true,
        spec: <<<'PHP'
            <?php

            namespace Foo;

            class A {
                public function a() {}
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

            PHP,
    ),

    'Declaration of an exposed class' => SpecWithConfig::create(
        exposeClasses: ['Foo\A'],
        expectedRecordedClasses: [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        spec: <<<'PHP'
            <?php

            namespace Foo;

            class A {
                public function a() {}
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
            \class_alias('Humbug\Foo\A', 'Foo\A', \false);

            PHP,
    ),

    // This is a pure anti-regression test – no need to excessively test this
    // in the other spec files
    'Declaration of an exposed class exposed via a pattern' => SpecWithConfig::create(
        exposeClasses: ['/^Foo\\\\A$/'],
        expectedRecordedClasses: [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        spec: <<<'PHP'
            <?php

            namespace Foo;

            class A {
                public function a() {}
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
            \class_alias('Humbug\Foo\A', 'Foo\A', \false);

            PHP,
    ),

    'Multiple declarations in different namespaces' => <<<'PHP'
        <?php

        namespace Foo {

            class A {
                public function a() {}
            }
        }

        namespace Bar {

            class B {
                public function b() {}
            }
        }

        namespace {

            class C {
                public function c() {}
            }
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

        class B
        {
            public function b()
            {
            }
        }
        namespace Humbug;

        class C
        {
            public function c()
            {
            }
        }

        PHP,

    'Multiple declarations in different namespaces with exposed classes' => SpecWithConfig::create(
        exposeClasses: [
            'Foo\A',
            'Bar\B',
        ],
        expectedRecordedClasses: [
            ['Foo\A', 'Humbug\Foo\A'],
            ['Bar\B', 'Humbug\Bar\B'],
        ],
        spec: <<<'PHP'
            <?php

            namespace Foo {

                class A {
                    public function a() {}
                }

                class B {
                    public function b() {}
                }

                class C {
                    public function c() {}
                }
            }

            namespace Bar {

                class A {
                    public function a() {}
                }

                class B {
                    public function b() {}
                }

                class C {
                    public function c() {}
                }
            }

            namespace {

                class A {
                    public function a() {}
                }

                class B {
                    public function b() {}
                }

                class C {
                    public function c() {}
                }
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
            \class_alias('Humbug\Foo\A', 'Foo\A', \false);
            class B
            {
                public function b()
                {
                }
            }
            class C
            {
                public function c()
                {
                }
            }
            namespace Humbug\Bar;

            class A
            {
                public function a()
                {
                }
            }
            class B
            {
                public function b()
                {
                }
            }
            \class_alias('Humbug\Bar\B', 'Bar\B', \false);
            class C
            {
                public function c()
                {
                }
            }
            namespace Humbug;

            class A
            {
                public function a()
                {
                }
            }
            class B
            {
                public function b()
                {
                }
            }
            class C
            {
                public function c()
                {
                }
            }

            PHP,
    ),
];
