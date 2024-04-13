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
        title: 'Class static property call of a namespaced class in the global scope',
    ),

    'Constant call on a namespaced class' => <<<'PHP'
        <?php

        namespace PHPUnit {
            class Command {}
        }

        namespace {
            PHPUnit\Command::$mainStaticProp;
        }
        ----
        <?php

        namespace Humbug\PHPUnit;

        class Command
        {
        }
        namespace Humbug;

        PHPUnit\Command::$mainStaticProp;

        PHP,

    'FQ constant call on a namespaced class' => <<<'PHP'
        <?php

        namespace PHPUnit {
            class Command {}
        }

        namespace {
            \PHPUnit\Command::$mainStaticProp;
        }
        ----
        <?php

        namespace Humbug\PHPUnit;

        class Command
        {
        }
        namespace Humbug;

        \Humbug\PHPUnit\Command::$mainStaticProp;

        PHP,

    'Constant call on an exposed namespaced class' => [
        'expose-classes' => ['PHPUnit\Command'],
        'expected-recorded-classes' => [
            ['PHPUnit\Command', 'Humbug\PHPUnit\Command'],
        ],
        'payload' => <<<'PHP'
            <?php

            namespace PHPUnit {
                class Command {}
            }

            namespace {
                PHPUnit\Command::$mainStaticProp;
            }
            ----
            <?php

            namespace Humbug\PHPUnit;

            class Command
            {
            }
            \class_alias('Humbug\\PHPUnit\\Command', 'PHPUnit\\Command', \false);
            namespace Humbug;

            \Humbug\PHPUnit\Command::$mainStaticProp;

            PHP,
    ],

    'FQ constant call on an exposed namespaced class' => [
        'expose-classes' => ['PHPUnit\Command'],
        'expected-recorded-classes' => [
            ['PHPUnit\Command', 'Humbug\PHPUnit\Command'],
        ],
        'payload' => <<<'PHP'
            <?php

            namespace PHPUnit {
                class Command {}
            }

            namespace {
                \PHPUnit\Command::$mainStaticProp;
            }
            ----
            <?php

            namespace Humbug\PHPUnit;

            class Command
            {
            }
            \class_alias('Humbug\\PHPUnit\\Command', 'PHPUnit\\Command', \false);
            namespace Humbug;

            \Humbug\PHPUnit\Command::$mainStaticProp;

            PHP,
    ],
];
