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

use Humbug\PhpScoper\Scoper\Spec\Meta;
use Humbug\PhpScoper\Scoper\Spec\SpecWithConfig;

return [
    'meta' => new Meta(
        title: 'Final class declaration',
    ),

    'Declaration in the global namespace' => <<<'PHP'
        <?php

        final class A {}
        ----
        <?php

        namespace Humbug;

        final class A
        {
        }

        PHP,

    'Declaration in the global namespace with global classes exposed' => SpecWithConfig::create(
        exposeGlobalClasses: true,
        expectedRecordedClasses: [
            ['A', 'Humbug\A'],
        ],
        spec: <<<'PHP'
            <?php

            final class A {}
            ----
            <?php

            namespace Humbug;

            final class A
            {
            }
            \class_alias('Humbug\\A', 'A', \false);

            PHP,
    ),

    'Declaration in a namespace' => <<<'PHP'
        <?php

        namespace Foo;

        final class A {}
        ----
        <?php

        namespace Humbug\Foo;

        final class A
        {
        }

        PHP,

    'Declaration in a namespace with global classes exposed' => SpecWithConfig::create(
        exposeGlobalClasses: true,
        spec: <<<'PHP'
            <?php

            namespace Foo;

            final class A {}
            ----
            <?php

            namespace Humbug\Foo;

            final class A
            {
            }

            PHP,
    ),

    'Declaration of an exposed final class' => SpecWithConfig::create(
        exposeClasses: ['Foo\A'],
        expectedRecordedClasses: [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        spec: <<<'PHP'
            <?php

            namespace Foo;

            final class A {}
            ----
            <?php

            namespace Humbug\Foo;

            final class A
            {
            }
            \class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);

            PHP,
    ),

    'Multiple declarations in different namespaces' => <<<'PHP'
        <?php

        namespace X {
            final class A {}
        }

        namespace Y {
            final class B {}
        }

        namespace Z {
            final class C {}
        }
        ----
        <?php

        namespace Humbug\X;

        final class A
        {
        }
        namespace Humbug\Y;

        final class B
        {
        }
        namespace Humbug\Z;

        final class C
        {
        }

        PHP,
];
