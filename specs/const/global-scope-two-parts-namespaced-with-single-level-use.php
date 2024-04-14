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
        title: 'Two-parts namespaced constant call in the global scope with a single-level use statement',
    ),

    'Namespaced constant call with namespace partially imported' => <<<'PHP'
        <?php

        class Foo {}

        use Foo;

        Foo\Bar\DUMMY_CONST;
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        use Humbug\Foo;
        Foo\Bar\DUMMY_CONST;

        PHP,

    'FQ namespaced constant call with namespace partially imported' => <<<'PHP'
        <?php

        class Foo {}

        use Foo;

        \Foo\Bar\DUMMY_CONST;
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        use Humbug\Foo;
        \Humbug\Foo\Bar\DUMMY_CONST;

        PHP,

    'Exposed namespaced constant call with namespace partially imported' => SpecWithConfig::create(
        exposeConstants: ['Foo\Bar\DUMMY_CONST'],
        spec: <<<'PHP'
            <?php

            class Foo {}

            use Foo;

            Foo\Bar\DUMMY_CONST;
            ----
            <?php

            namespace Humbug;

            class Foo
            {
            }
            use Humbug\Foo;
            \Foo\Bar\DUMMY_CONST;

            PHP,
    ),
];
