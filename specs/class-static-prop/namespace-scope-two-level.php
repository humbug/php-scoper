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
        title: 'Class static property call of a namespaced class in a namespace',
    ),

    'Constant call on a namespaced class' => <<<'PHP'
        <?php

        namespace X\PHPUnit {
            class Command {}
        }

        namespace X {
            PHPUnit\Command::$mainStaticProp;
        }
        ----
        <?php

        namespace Humbug\X\PHPUnit;

        class Command
        {
        }
        namespace Humbug\X;

        PHPUnit\Command::$mainStaticProp;

        PHP,

    'FQ constant call on a namespaced class' => <<<'PHP'
        <?php

        namespace PHPUnit {
            class Command {}
        }

        namespace X {
            \PHPUnit\Command::$mainStaticProp;
        }
        ----
        <?php

        namespace Humbug\PHPUnit;

        class Command
        {
        }
        namespace Humbug\X;

        \Humbug\PHPUnit\Command::$mainStaticProp;

        PHP,

    'Constant call on an exposed namespaced class' => SpecWithConfig::create(
        exposeClasses: ['X\PHPUnit\Command'],
        expectedRecordedClasses: [
            ['X\PHPUnit\Command', 'Humbug\X\PHPUnit\Command'],
        ],
        spec: <<<'PHP'
            <?php

            namespace X\PHPUnit {
                class Command {}
            }

            namespace X {
                PHPUnit\Command::$mainStaticProp;
            }
            ----
            <?php

            namespace Humbug\X\PHPUnit;

            class Command
            {
            }
            \class_alias('Humbug\X\PHPUnit\Command', 'X\PHPUnit\Command', \false);
            namespace Humbug\X;

            PHPUnit\Command::$mainStaticProp;

            PHP,
    ),

    'FQ constant call on an exposed namespaced class' => SpecWithConfig::create(
        exposeClasses: ['PHPUnit\Command'],
        expectedRecordedClasses: [
            ['PHPUnit\Command', 'Humbug\PHPUnit\Command'],
        ],
        spec: <<<'PHP'
            <?php

            namespace PHPUnit {
                class Command {}
            }

            namespace X {
                \PHPUnit\Command::$mainStaticProp;
            }
            ----
            <?php

            namespace Humbug\PHPUnit;

            class Command
            {
            }
            \class_alias('Humbug\PHPUnit\Command', 'PHPUnit\Command', \false);
            namespace Humbug\X;

            \Humbug\PHPUnit\Command::$mainStaticProp;

            PHP,
    ),
];
