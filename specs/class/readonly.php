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
        title: 'Readonly class declaration',
    ),

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

    'Declaration in the global namespace with global classes exposed' => SpecWithConfig::create(
        exposeGlobalClasses: true,
        expectedRecordedClasses: [
            ['A', 'Humbug\A'],
        ],
        spec: <<<'PHP'
            <?php

            readonly class A {}
            ----
            <?php

            namespace Humbug;

            readonly class A
            {
            }
            \class_alias('Humbug\A', 'A', \false);

            PHP,
    ),

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

    'Declaration in a namespace with global classes exposed' => SpecWithConfig::create(
        exposeGlobalClasses: true,
        spec: <<<'PHP'
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
    ),

    'Declaration of an exposed readonly class' => SpecWithConfig::create(
        exposeClasses: ['Foo\A'],
        expectedRecordedClasses: [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        spec: <<<'PHP'
            <?php

            namespace Foo;

            readonly class A {}
            ----
            <?php

            namespace Humbug\Foo;

            readonly class A
            {
            }
            \class_alias('Humbug\Foo\A', 'Foo\A', \false);

            PHP,
    ),

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
