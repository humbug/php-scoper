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
        title: 'Class constant call of a class imported with an aliased use statement in the global scope',
    ),

    'Constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        class Foo {}

        use Foo as X;

        X::MAIN_CONST;
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        use Humbug\Foo as X;
        X::MAIN_CONST;

        PHP,

    'FQ constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        class Foo {}
        class X {}

        use Foo as X;

        \X::MAIN_CONST;
        ----
        <?php

        namespace Humbug;

        class Foo
        {
        }
        class X
        {
        }
        use Humbug\Foo as X;
        \Humbug\X::MAIN_CONST;

        PHP,

    'Constant call on an internal class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        use Reflector as X;

        X::MAIN_CONST;
        ----
        <?php

        namespace Humbug;

        use Reflector as X;
        X::MAIN_CONST;

        PHP,

    'FQ constant call on an internal class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        class X {}

        use Reflector as X;

        \X::MAIN_CONST;
        ----
        <?php

        namespace Humbug;

        class X
        {
        }
        use Reflector as X;
        \Humbug\X::MAIN_CONST;

        PHP,

    'Constant call on an exposed class which is imported via an aliased use statement and which belongs to the global namespace' => SpecWithConfig::create(
        exposeClasses: ['Foo'],
        spec: <<<'PHP'
            <?php

            use Foo as X;

            X::MAIN_CONST;
            ----
            <?php

            namespace Humbug;

            use Humbug\Foo as X;
            X::MAIN_CONST;

            PHP,
    ),

    'FQ constant call on an excluded class which is imported via an aliased use statement and which belongs to the global namespace' => SpecWithConfig::create(
        exposeClasses: ['Foo'],
        spec: <<<'PHP'
            <?php

            class X {}

            use Foo as X;

            \X::MAIN_CONST;
            ----
            <?php

            namespace Humbug;

            class X
            {
            }
            use Humbug\Foo as X;
            \Humbug\X::MAIN_CONST;

            PHP,
    ),
];
