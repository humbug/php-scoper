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

return [
    'meta' => new Meta(
        title: 'Class static property call in the global scope',
    ),

    'Constant call on a class belonging to the global namespace' => <<<'PHP'
        <?php

        class Command {}

        Command::$mainStaticProp;
        ----
        <?php

        namespace Humbug;

        class Command
        {
        }
        Command::$mainStaticProp;

        PHP,

    'Constant call on a class belonging to the global namespace which is excluded' => [
        'exclude-namespaces' => ['/^$/'],
        'expected-recorded-classes' => [
            ['Command', 'Humbug\Command'],
        ],
        'payload' => <<<'PHP'
            <?php

            class Command {}

            Command::$mainStaticProp;
            ----
            <?php

            namespace {
                class Command
                {
                }
                \class_alias('Humbug\\Command', 'Command', \false);
                \Command::$mainStaticProp;
            }

            PHP,
    ],

    'FQ constant call on a class belonging to the global namespace' => <<<'PHP'
        <?php

        class Command {}

        \Command::$mainStaticProp;
        ----
        <?php

        namespace Humbug;

        class Command
        {
        }
        \Humbug\Command::$mainStaticProp;

        PHP,

    'Constant call on an internal class belonging to the global namespace' => <<<'PHP'
        <?php

        Reflector::$mainStaticProp;
        ----
        <?php

        namespace Humbug;

        \Reflector::$mainStaticProp;

        PHP,

    'FQ constant call on an internal class belonging to the global namespace' => <<<'PHP'
        <?php

        \Reflector::$mainStaticProp;
        ----
        <?php

        namespace Humbug;

        \Reflector::$mainStaticProp;

        PHP,

    // TODO: this should not have been made into a FQC call
    'Constant call on an exposed class belonging to the global namespace' => [
        'expose-classes' => ['Foo'],
        'payload' => <<<'PHP'
            <?php

            Foo::$mainStaticProp;
            ----
            <?php

            namespace Humbug;

            \Humbug\Foo::$mainStaticProp;

            PHP,
    ],

    'FQ constant call on an exposed class belonging to the global namespace' => [
        'expose-classes' => ['Foo'],
        'payload' => <<<'PHP'
            <?php

            \Foo::$mainStaticProp;
            ----
            <?php

            namespace Humbug;

            \Humbug\Foo::$mainStaticProp;

            PHP,
    ],
];
