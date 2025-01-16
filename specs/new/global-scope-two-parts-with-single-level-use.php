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
        title: 'New statement call of a namespaced class imported with a use statement in the global scope',
    ),

    'New statement call of a namespaced class partially imported with a use statement' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace Foo {
            class Bar {}
        }

        namespace {
            use Foo;

            new Foo\Bar();
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

        use Humbug\Foo;
        new Foo\Bar();

        PHP,

    'New statement call of an exposed namespaced class partially imported with a use statement' => SpecWithConfig::create(
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
                use Foo;

                new Foo\Bar();
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

            use Humbug\Foo;
            new Foo\Bar();

            PHP,
    ),

    'FQ new statement call of a namespaced class partially imported with a use statement' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace Foo {
            class Bar {}
        }

        namespace {
            use Foo;

            new \Foo\Bar();
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

        use Humbug\Foo;
        new \Humbug\Foo\Bar();

        PHP,

    'FQ new statement call of an exposed namespaced class partially imported with a use statement' => SpecWithConfig::create(
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
                use Foo;

                new \Foo\Bar();
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

            use Humbug\Foo;
            new \Humbug\Foo\Bar();

            PHP,
    ),

    'New statement call of a namespaced class imported with a use statement' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace Foo {
            class Bar {}
        }

        namespace {
            use Foo\Bar;

            new Bar();
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

        use Humbug\Foo\Bar;
        new Bar();

        PHP,

    'New statement call of an exposed namespaced class imported with a use statement' => SpecWithConfig::create(
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
                use Foo\Bar;

                new Bar();
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

            use Humbug\Foo\Bar;
            new Bar();

            PHP,
    ),

    'FQ new statement call of a namespaced class imported with a use statement' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
            class Bar {}
        }

        namespace Foo {
            class Bar {}
        }

        namespace {
            use Foo\Bar;

            new \Bar();
        }
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        class Bar
        {
        }
        namespace Humbug\Foo;

        class Bar
        {
        }
        namespace Humbug;

        use Humbug\Foo\Bar;
        new \Humbug\Bar();

        PHP,

    'FQ new statement call of a, exposed namespaced class imported with a use statement' => SpecWithConfig::create(
        exposeClasses: ['Foo\Bar'],
        expectedRecordedClasses: [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        spec: <<<'PHP'
            <?php

            namespace {
                class Foo {}
                class Bar {}
            }

            namespace Foo {
                class Bar {}
            }

            namespace {
                use Foo\Bar;

                new \Bar();
            }
            ----
            <?php

            namespace Humbug;

            class Foo
            {
            }
            class Bar
            {
            }
            namespace Humbug\Foo;

            class Bar
            {
            }
            \class_alias('Humbug\Foo\Bar', 'Foo\Bar', \false);
            namespace Humbug;

            use Humbug\Foo\Bar;
            new \Humbug\Bar();

            PHP,
    ),
];
