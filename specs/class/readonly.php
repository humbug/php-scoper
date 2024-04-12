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
        'title' => 'Readonly class declaration',
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

    'Declaration in the global namespace' => <<<'PHP'
        <?php

        readonly class A {}
        ----
        <?php

        namespace Humbug;

        readonly class A
        {
        }

        PHP,

    'Declaration in the global namespace with global classes exposed' => [
        'expose-global-classes' => true,
        'expected-recorded-classes' => [
            ['A', 'Humbug\A'],
        ],
        'payload' => <<<'PHP'
            <?php

            readonly class A {}
            ----
            <?php

            namespace Humbug;

            readonly class A
            {
            }
            \class_alias('Humbug\\A', 'A', \false);

            PHP,
    ],

    'Declaration in a namespace' => <<<'PHP'
        <?php

        namespace Foo;

        readonly class A {}
        ----
        <?php

        namespace Humbug\Foo;

        readonly class A
        {
        }

        PHP,

    'Declaration in a namespace with global classes exposed' => [
        'expose-global-classes' => true,
        'payload' => <<<'PHP'
            <?php

            namespace Foo;

            readonly class A {}
            ----
            <?php

            namespace Humbug\Foo;

            readonly class A
            {
            }

            PHP,
    ],

    'Declaration of an exposed readonly class' => [
        'expose-classes' => ['Foo\A'],
        'expected-recorded-classes' => [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        'payload' => <<<'PHP'
            <?php

            namespace Foo;

            readonly class A {}
            ----
            <?php

            namespace Humbug\Foo;

            readonly class A
            {
            }
            \class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);

            PHP,
    ],

    'Multiple declarations in different namespaces' => <<<'PHP'
        <?php

        namespace X {
            readonly class A {}
        }

        namespace Y {
            readonly class B {}
        }

        namespace Z {
            readonly class C {}
        }
        ----
        <?php

        namespace Humbug\X;

        readonly class A
        {
        }
        namespace Humbug\Y;

        readonly class B
        {
        }
        namespace Humbug\Z;

        readonly class C
        {
        }

        PHP,
];
