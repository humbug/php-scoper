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
        'title' => 'Namespaced function call imported with an aliased use statement in the global scope',
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

    'Namespaced function call imported with a partial use statement in the global scope' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace Foo {
            function main() {}
        }

        namespace {
            use Foo as X;

            X\main();
        }
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        namespace Humbug\Foo;

        function main()
        {
        }
        namespace Humbug;

        use Humbug\Foo as X;
        X\main();

        PHP,

    'FQ namespaced function call imported with a partial use statement in the global scope' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace X {
            function main() {}
        }

        namespace {
            use Foo as X;

            \X\main();
        }
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        namespace Humbug\X;

        function main()
        {
        }
        namespace Humbug;

        use Humbug\Foo as X;
        \Humbug\X\main();

        PHP,

    'Exposed namespaced function call imported with a partial use statement in the global scope' => [
        'expose-functions' => ['Foo\main'],
        'expected-recorded-functions' => [
            ['Foo\main', 'Humbug\Foo\main'],
        ],
        'payload' => <<<'PHP'
            <?php

            namespace {
                class Foo {}
            }

            namespace Foo {
                function main() {}
            }

            namespace {
                use Foo as X;

                X\main();
            }
            ----
            <?php

            namespace Humbug;

            class Foo
            {
            }
            namespace Humbug\Foo;

            function main()
            {
            }
            namespace Humbug;

            use Humbug\Foo as X;
            X\main();

            PHP,
    ],
];
