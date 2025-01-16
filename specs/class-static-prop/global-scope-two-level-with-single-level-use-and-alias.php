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
        title: 'Class static property call of a namespaced class imported with an aliased use statement in the global scope',
    ),

    'Constant call on a namespaced class partially imported with an aliased use statement' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace Foo {
            class Bar {}
        }

        namespace {
            use Foo as X;

            X\Bar::$mainStaticProp;
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

        use Humbug\Foo as X;
        X\Bar::$mainStaticProp;

        PHP,

    'Constant call on a namespaced class imported with an aliased use statement' => <<<'PHP'
        <?php

        namespace Foo {
            class Bar {}
        }

        namespace {
            use Foo\Bar as X;

            X::$mainStaticProp;
        }
        ----
        <?php

        namespace Humbug\Foo;

        class Bar
        {
        }
        namespace Humbug;

        use Humbug\Foo\Bar as X;
        X::$mainStaticProp;

        PHP,

    'FQ constant call on a namespaced class imported with an aliased use statement' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace X {
            class Bar {}
        }

        namespace {
            use Foo as X;

            \X\Bar::$mainStaticProp;
        }
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        namespace Humbug\X;

        class Bar
        {
        }
        namespace Humbug;

        use Humbug\Foo as X;
        \Humbug\X\Bar::$mainStaticProp;

        PHP,

    'FQ Constant call on an exposed namespaced class partially imported with an aliased use statement' => SpecWithConfig::create(
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
                use Foo as X;

                X\Bar::$mainStaticProp;
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

            use Humbug\Foo as X;
            X\Bar::$mainStaticProp;

            PHP,
    ),

    'FQ constant call on an exposed namespaced class imported with an aliased use statement' => SpecWithConfig::create(
        exposeClasses: ['Foo\Bar'],
        spec: <<<'PHP'
            <?php

            namespace {
                class Foo {}
            }

            namespace X {
                class Bar {}
            }

            namespace {
                use Foo as X;

                \X\Bar::$mainStaticProp;
            }
            ----
            <?php

            namespace Humbug;

            class Foo
            {
            }
            namespace Humbug\X;

            class Bar
            {
            }
            namespace Humbug;

            use Humbug\Foo as X;
            \Humbug\X\Bar::$mainStaticProp;

            PHP,
    ),
];
