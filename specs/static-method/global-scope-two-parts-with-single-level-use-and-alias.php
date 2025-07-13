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
        title: 'Static method call statement of a namespaced class imported with an aliased use statement in the global scope',
    ),

    'Static method call statement of a namespaced class partially imported with an aliased use statement' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace Foo {
            class Bar {}
        }

        namespace {
            use Foo as A;

            A\Bar::main();
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
        namespace Humbug;

        use Humbug\Foo as A;
        A\Bar::main();

        PHP,

    'Static method call statement of a namespaced class imported with an aliased use statement' => <<<'PHP'
        <?php

        namespace Foo {
            class Bar {}
        }

        namespace {
            use Foo\Bar as A;

            A::main();
        }
        ----
        <?php

        namespace Humbug\Foo;

        class Bar
        {
        }
        namespace Humbug;

        use Humbug\Foo\Bar as A;
        A::main();

        PHP,

    'FQ static method call statement of a namespaced class partially imported with an aliased use statement' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace A {
            class Bar {}
        }

        namespace {
            use Foo as A;

            \A\Bar::main();
        }
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        namespace Humbug\A;

        class Bar
        {
        }
        namespace Humbug;

        use Humbug\Foo as A;
        \Humbug\A\Bar::main();

        PHP,

    'FQ static method call statement of a namespaced class imported with an aliased use statement' => <<<'PHP'
        <?php

        namespace Foo {
            class Bar {}
        }

        namespace {
            class A {}

            use Foo\Bar as A;

            \A::main();
        }
        ----
        <?php

        namespace Humbug\Foo;

        class Bar
        {
        }
        namespace Humbug;

        class A
        {
        }
        use Humbug\Foo\Bar as A;
        \Humbug\A::main();

        PHP,

    'Static method call statement of an exposed namespaced class partially imported with an aliased use statement' => SpecWithConfig::create(
        exposeClasses: ['Foo\Bar'],
        expectedRecordedClasses: [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        spec: <<<'PHP'
            <?php

            namespace {
                class Foo {}
            }

            namespace Foo {
                class Bar {}
            }

            namespace {
                use Foo as A;

                A\Bar::main();
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
            \class_alias('Humbug\Foo\Bar', 'Foo\Bar', \false);
            namespace Humbug;

            use Humbug\Foo as A;
            A\Bar::main();

            PHP,
    ),

    'Static method call statement of an exposed namespaced class imported with an aliased use statement' => SpecWithConfig::create(
        exposeClasses: ['Foo\Bar'],
        expectedRecordedClasses: [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        spec: <<<'PHP'
            <?php

            namespace Foo {
                class Bar {}
            }

            namespace {
                use Foo\Bar as A;

                A::main();
            }
            ----
            <?php

            namespace Humbug\Foo;

            class Bar
            {
            }
            \class_alias('Humbug\Foo\Bar', 'Foo\Bar', \false);
            namespace Humbug;

            use Humbug\Foo\Bar as A;
            A::main();

            PHP,
    ),

    'FQ static method call statement of an exposed namespaced class partially imported with an aliased use statement' => SpecWithConfig::create(
        exposeClasses: ['Foo\Bar'],
        spec: <<<'PHP'
            <?php

            namespace {
                class Foo {}
            }

            namespace A {
                class Bar {}
            }

            namespace {
                use Foo as A;

                \A\Bar::main();
            }
            ----
            <?php

            namespace Humbug;

            class Foo
            {
            }
            namespace Humbug\A;

            class Bar
            {
            }
            namespace Humbug;

            use Humbug\Foo as A;
            \Humbug\A\Bar::main();

            PHP,
    ),

    'FQ static method call statement of an exposed namespaced class imported with an aliased use statement' => SpecWithConfig::create(
        exposeClasses: ['Foo\Bar'],
        expectedRecordedClasses: [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        spec: <<<'PHP'
            <?php

            namespace {
                class A {}
            }

            namespace Foo {
                class Bar {}
            }

            namespace {
                use Foo\Bar as A;

                \A::main();
            }
            ----
            <?php

            namespace Humbug;

            class A
            {
            }
            namespace Humbug\Foo;

            class Bar
            {
            }
            \class_alias('Humbug\Foo\Bar', 'Foo\Bar', \false);
            namespace Humbug;

            use Humbug\Foo\Bar as A;
            \Humbug\A::main();

            PHP,
    ),
];
