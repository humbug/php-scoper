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
        title: 'Class static property call of a class imported with a use statement in the global scope',
    ),

    'Constant call on a class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        class Command {}

        use Command;

        Command::$mainStaticProp;
        ----
        <?php

        namespace Humbug;

        class Command
        {
        }
        use Humbug\Command;
        Command::$mainStaticProp;

        PHP,

    'FQ constant call on a class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        class Command {}

        use Command;

        \Command::$mainStaticProp;
        ----
        <?php

        namespace Humbug;

        class Command
        {
        }
        use Humbug\Command;
        \Humbug\Command::$mainStaticProp;

        PHP,

    'Constant call on an internal class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        use Reflector;

        Reflector::$mainStaticProp;
        ----
        <?php

        namespace Humbug;

        use Reflector;
        Reflector::$mainStaticProp;

        PHP,

    'FQ constant call on an internal class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
        <?php

        use Reflector;

        \Reflector::$mainStaticProp;
        ----
        <?php

        namespace Humbug;

        use Reflector;
        \Reflector::$mainStaticProp;

        PHP,

    'Constant call on an exposed class which is imported via a use statement and which belongs to the global namespace' => SpecWithConfig::create(
        exposeClasses: ['Foo'],
        spec: <<<'PHP'
            <?php

            use Foo;

            Foo::$mainStaticProp;
            ----
            <?php

            namespace Humbug;

            use Humbug\Foo;
            Foo::$mainStaticProp;

            PHP,
    ),

    'FQ constant call on an exposed class which is imported via a use statement and which belongs to the global namespace' => SpecWithConfig::create(
        exposeClasses: ['Foo'],
        spec: <<<'PHP'
            <?php

            use Foo;

            \Foo::$mainStaticProp;
            ----
            <?php

            namespace Humbug;

            use Humbug\Foo;
            \Humbug\Foo::$mainStaticProp;

            PHP,
    ),
];
