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
        title: 'Class constant call in the global scope',
    ),

    'Constant call on a class belonging to the global namespace' => <<<'PHP'
        <?php

        class Command {}

        Command::MAIN_CONST;
        ----
        <?php

        namespace Humbug;

        class Command
        {
        }
        Command::MAIN_CONST;

        PHP,

    'Constant call on a class belonging to the global namespace which is excluded' => SpecWithConfig::create(
        excludeNamespaces: ['/^$/'],
        expectedRecordedClasses: [
            ['Command', 'Humbug\Command'],
        ],
        spec: <<<'PHP'
            <?php

            class Command {}

            Command::MAIN_CONST;
            ----
            <?php

            namespace {
                class Command
                {
                }
                \class_alias('Humbug\\Command', 'Command', \false);
                \Command::MAIN_CONST;
            }

            PHP,
    ),

    'FQ constant call on a class belonging to the global namespace' => <<<'PHP'
        <?php

        class Command {}

        \Command::MAIN_CONST;
        ----
        <?php

        namespace Humbug;

        class Command
        {
        }
        \Humbug\Command::MAIN_CONST;

        PHP,

    'Constant call on an internal class belonging to the global namespace' => <<<'PHP'
        <?php

        Reflector::MAIN_CONST;
        ----
        <?php

        namespace Humbug;

        \Reflector::MAIN_CONST;

        PHP,

    'FQ constant call on an internal class belonging to the global namespace' => <<<'PHP'
        <?php

        \Reflector::MAIN_CONST;
        ----
        <?php

        namespace Humbug;

        \Reflector::MAIN_CONST;

        PHP,

    'Constant call on an exposed class belonging to the global namespace' => SpecWithConfig::create(
        exposeClasses: ['Foo'],
        spec: <<<'PHP'
            <?php

            Foo::MAIN_CONST;
            ----
            <?php

            namespace Humbug;

            \Humbug\Foo::MAIN_CONST;

            PHP,
    ),

    'FQ constant call on an exposed class belonging to the global namespace' => SpecWithConfig::create(
        exposeClasses: ['Foo'],
        spec: <<<'PHP'
            <?php

            \Foo::MAIN_CONST;
            ----
            <?php

            namespace Humbug;

            \Humbug\Foo::MAIN_CONST;

            PHP,
    ),
];
