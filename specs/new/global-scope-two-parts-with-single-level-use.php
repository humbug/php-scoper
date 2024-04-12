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
        'title' => 'New statement call of a namespaced class imported with a use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

        'expose-global-constants' => false,
        'expose-global-classes' => false,
        'expose-global-functions' => false,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'expected-recorded-classes' => [],
        'expected-recorded-functions' => [],
    ],

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

    'New statement call of an exposed namespaced class partially imported with a use statement' => [
        'expose-classes' => ['Foo\Bar'],
        'expected-recorded-classes' => [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
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
            \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
            namespace Humbug;

            use Humbug\Foo;
            new Foo\Bar();

            PHP,
    ],

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

    'FQ new statement call of an exposed namespaced class partially imported with a use statement' => [
        'expose-classes' => ['Foo\Bar'],
        'expected-recorded-classes' => [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
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
            \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
            namespace Humbug;

            use Humbug\Foo;
            new \Humbug\Foo\Bar();

            PHP,
    ],

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

    'New statement call of an exposed namespaced class imported with a use statement' => [
        'expose-classes' => ['Foo\Bar'],
        'expected-recorded-classes' => [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
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
            \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
            namespace Humbug;

            use Humbug\Foo\Bar;
            new Bar();

            PHP,
    ],

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

    'FQ new statement call of a, exposed namespaced class imported with a use statement' => [
        'expose-classes' => ['Foo\Bar'],
        'expected-recorded-classes' => [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
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
            \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
            namespace Humbug;

            use Humbug\Foo\Bar;
            new \Humbug\Bar();

            PHP,
    ],
];
