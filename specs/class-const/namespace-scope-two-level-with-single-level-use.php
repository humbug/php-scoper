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
        title: 'Class constant call of a namespaced class imported with a use statement in a namespace',
    ),

    'Constant call on a namespaced class partially imported with a use statement' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace Foo {
            class Bar {}
        }

        namespace X {
            use Foo;

            Foo\Bar::MAIN_CONST;
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

        use Humbug\Foo;
        Foo\Bar::MAIN_CONST;

        PHP,

    'Constant call on a namespaced class imported with a use statement' => <<<'PHP'
        <?php

        namespace Foo {
            class Bar {}
        }

        namespace Foo\Bar {
            class X {}
        }

        namespace X {
            use Foo\Bar;

            Bar\X::MAIN_CONST;
        }
        ----
        <?php

        namespace Humbug\Foo;

        class Bar
        {
        }
        namespace Humbug\Foo\Bar;

        class X
        {
        }
        namespace Humbug\X;

        use Humbug\Foo\Bar;
        Bar\X::MAIN_CONST;

        PHP,

    'FQ constant call on a namespaced class imported with a use statement' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace Foo {
            class Bar {}
        }

        namespace X {
            use Foo;

            \Foo\Bar::MAIN_CONST;
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

        use Humbug\Foo;
        \Humbug\Foo\Bar::MAIN_CONST;

        PHP,

    'FQ Constant call on an exposed namespaced class partially imported with a use statement' => SpecWithConfig::create(
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

            namespace X {
                use Foo;

                Foo\Bar::MAIN_CONST;
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
            namespace Humbug\X;

            use Humbug\Foo;
            Foo\Bar::MAIN_CONST;

            PHP,
    ),

    'FQ constant call on an exposed namespaced class imported with a use statement' => SpecWithConfig::create(
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

            namespace X {
                use Foo;

                \Foo\Bar::MAIN_CONST;
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
            namespace Humbug\X;

            use Humbug\Foo;
            \Humbug\Foo\Bar::MAIN_CONST;

            PHP,
    ),
];
