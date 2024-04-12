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
        'title' => 'Class constant call of a class imported with a use statement in a namespace',
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

    'Constant call on a class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        namespace {
            class Foo {}
        }

        namespace X {
            use Foo;

            Foo::MAIN_CONST;
        }
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        namespace Humbug\X;

        use Humbug\Foo;
        Foo::MAIN_CONST;

        PHP,

    'FQ constant call on a class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        namespace {
            class Command {}
        }

        namespace X {
            use Command;

            \Command::MAIN_CONST;
        }
        ----
        <?php

        namespace Humbug;

        class Command
        {
        }
        namespace Humbug\X;

        use Humbug\Command;
        \Humbug\Command::MAIN_CONST;

        PHP,

    'Constant call on an internal class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        namespace X;

        use Reflector;

        Reflector::MAIN_CONST;
        ----
        <?php

        namespace Humbug\X;

        use Reflector;
        Reflector::MAIN_CONST;

        PHP,

    'FQ constant call on an internal class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        namespace X;

        use Reflector;

        \Reflector::MAIN_CONST;
        ----
        <?php

        namespace Humbug\X;

        use Reflector;
        \Reflector::MAIN_CONST;

        PHP,

    'Constant call on an exposed class which is imported via a use statement and which belongs to the global namespace' => [
        'expose-classes' => ['Foo'],
        'payload' => <<<'PHP'
            <?php

            namespace X;

            use Foo;

            Foo::MAIN_CONST;
            ----
            <?php

            namespace Humbug\X;

            use Humbug\Foo;
            Foo::MAIN_CONST;

            PHP,
    ],

    'FQ constant call on an exposed class which is imported via a use statement and which belongs to the global namespace' => [
        'expose-classes' => ['Foo'],
        'payload' => <<<'PHP'
            <?php

            namespace X;

            use Foo;

            \Foo::MAIN_CONST;
            ----
            <?php

            namespace Humbug\X;

            use Humbug\Foo;
            \Humbug\Foo::MAIN_CONST;

            PHP,
    ],
];
