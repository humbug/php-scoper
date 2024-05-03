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
        title: 'Static method call statement of a namespaced class imported with a use statement in a namespace',
    ),

    'Static method call statement of a class via a use statement' => <<<'PHP'
        <?php

        namespace X {
            class Foo {}
        }

        namespace X\Foo {
            class Bar {}
        }

        namespace A {
            use X\Foo;

            Foo\Bar::main();
        }
        ----
        <?php

        namespace Humbug\X;

        class Foo
        {
        }
        namespace Humbug\X\Foo;

        class Bar
        {
        }
        namespace Humbug\A;

        use Humbug\X\Foo;
        Foo\Bar::main();

        PHP,

    'FQ static method call statement of a class via a use statement' => <<<'PHP'
        <?php

        namespace X {
            class Foo {}
        }

        namespace Foo {
            class Bar {}
        }

        namespace A {
            use X\Foo;

            \Foo\Bar::main();
        }
        ----
        <?php

        namespace Humbug\X;

        class Foo
        {
        }
        namespace Humbug\Foo;

        class Bar
        {
        }
        namespace Humbug\A;

        use Humbug\X\Foo;
        \Humbug\Foo\Bar::main();

        PHP,

    'Static method call statement of an exposed class via a use statement' => SpecWithConfig::create(
        exposeClasses: ['X\Foo\Bar'],
        expectedRecordedClasses: [
            ['X\Foo\Bar', 'Humbug\X\Foo\Bar'],
        ],
        spec: <<<'PHP'
            <?php

            namespace X {
                class Foo {}
            }

            namespace X\Foo {
                class Bar {}
            }

            namespace A {
                use X\Foo;

                Foo\Bar::main();
            }
            ----
            <?php

            namespace Humbug\X;

            class Foo
            {
            }
            namespace Humbug\X\Foo;

            class Bar
            {
            }
            \class_alias('Humbug\X\Foo\Bar', 'X\Foo\Bar', \false);
            namespace Humbug\A;

            use Humbug\X\Foo;
            Foo\Bar::main();

            PHP,
    ),

    'FQ static method call statement of a non-exposed class via a use statement' => SpecWithConfig::create(
        exposeClasses: ['X\Foo\Bar'],
        spec: <<<'PHP'
            <?php

            namespace X {
                class Foo {}
            }

            namespace Foo {
                class Bar {}
            }

            namespace A {
                use X\Foo;

                \Foo\Bar::main();
            }
            ----
            <?php

            namespace Humbug\X;

            class Foo
            {
            }
            namespace Humbug\Foo;

            class Bar
            {
            }
            namespace Humbug\A;

            use Humbug\X\Foo;
            \Humbug\Foo\Bar::main();

            PHP,
    ),
];
