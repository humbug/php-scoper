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
        title: 'String literal assigned as a constant',
    ),

    'FQCN string argument' => <<<'PHP'
        <?php

        const X = 'Yaml';
        const X = '\\Yaml';
        const X = 'Closure';
        const X = '\\Closure';
        const X = 'Symfony\\Component\\Yaml\\Ya_1';
        const X = '\\Symfony\\Component\\Yaml\\Ya_1';
        const X = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        const X = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';

        ----
        <?php

        namespace Humbug;

        const X = 'Yaml';
        const X = '\Yaml';
        const X = 'Closure';
        const X = '\Closure';
        const X = 'Humbug\Symfony\Component\Yaml\Ya_1';
        const X = 'Humbug\Symfony\Component\Yaml\Ya_1';
        const X = 'Humbug\Symfony\Component\Yaml\Ya_1';
        const X = 'Humbug\Symfony\Component\Yaml\Ya_1';

        PHP,

    'FQCN string argument on exposed class' => SpecWithConfig::create(
        exposeClasses: ['Symfony\Component\Yaml\Yaml'],
        spec: <<<'PHP'
            <?php

            const X = 'Symfony\\Component\\Yaml\\Ya_1l';
            const X = 'Symfony\\Component\\Yaml\\Ya_1';
            const X = '\\Symfony\\Component\\Yaml\\Ya_1';
            const X = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
            const X = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';

            ----
            <?php

            namespace Humbug;

            const X = 'Humbug\Symfony\Component\Yaml\Ya_1l';
            const X = 'Humbug\Symfony\Component\Yaml\Ya_1';
            const X = 'Humbug\Symfony\Component\Yaml\Ya_1';
            const X = 'Humbug\Symfony\Component\Yaml\Ya_1';
            const X = 'Humbug\Symfony\Component\Yaml\Ya_1';

            PHP,
    ),

    'FQCN string argument on classes belonging to an excluded namespace' => SpecWithConfig::create(
        excludeNamespaces: ['Symfony\Component'],
        spec: <<<'PHP'
            <?php

            const X = 'Symfony\\Yaml';
            const X = 'Symfony\\Component\\Yaml\\Ya_1';
            const X = '\\Symfony\\Component\\Yaml\\Ya_1';
            const X = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
            const X = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';

            ----
            <?php

            namespace Humbug;

            const X = 'Humbug\Symfony\Yaml';
            const X = 'Symfony\Component\Yaml\Ya_1';
            const X = '\Symfony\Component\Yaml\Ya_1';
            const X = 'Humbug\Symfony\Component\Yaml\Ya_1';
            const X = '\Humbug\Symfony\Component\Yaml\Ya_1';

            PHP,
    ),

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
        <?php

        const X = 'Symfony\\Component' . '\\Yaml\\Ya_1';
        const X = '\\Symfony\\Component' . '\\Yaml\\Ya_1';

        ----
        <?php

        namespace Humbug;

        const X = 'Symfony\Component' . '\Yaml\Ya_1';
        const X = '\Symfony\Component' . '\Yaml\Ya_1';

        PHP,

    'FQC constant call' => <<<'PHP'
        <?php

        namespace Symfony\Component\Yaml {
            class Yaml {}
        }

        namespace {
            const X = Symfony\Component\Yaml\Yaml::class;
            const X = \Symfony\Component\Yaml\Yaml::class;
            const X = Humbug\Symfony\Component\Yaml\Yaml::class;
            const X = \Humbug\Symfony\Component\Yaml\Yaml::class;
        }
        ----
        <?php

        namespace Humbug\Symfony\Component\Yaml;

        class Yaml
        {
        }
        namespace Humbug;

        const X = Symfony\Component\Yaml\Yaml::class;
        const X = \Humbug\Symfony\Component\Yaml\Yaml::class;
        const X = \Humbug\Symfony\Component\Yaml\Yaml::class;
        const X = \Humbug\Symfony\Component\Yaml\Yaml::class;

        PHP,

    'FQC constant call on exposed class' => SpecWithConfig::create(
        exposeClasses: ['Symfony\Component\Yaml\Ya_1'],
        expectedRecordedClasses: [
            ['Symfony\Component\Yaml\Ya_1', 'Humbug\Symfony\Component\Yaml\Ya_1'],
        ],
        spec: <<<'PHP'
            <?php

            namespace Symfony\Component\Yaml {
                class Ya_1 {}
            }

            namespace {
                const X = Symfony\Component\Yaml\Ya_1::class;
                const X = \Symfony\Component\Yaml\Ya_1::class;
                const X = Humbug\Symfony\Component\Yaml\Ya_1::class;
                const X = \Humbug\Symfony\Component\Yaml\Ya_1::class;
            }
            ----
            <?php

            namespace Humbug\Symfony\Component\Yaml;

            class Ya_1
            {
            }
            \class_alias('Humbug\Symfony\Component\Yaml\Ya_1', 'Symfony\Component\Yaml\Ya_1', \false);
            namespace Humbug;

            const X = \Humbug\Symfony\Component\Yaml\Ya_1::class;
            const X = \Humbug\Symfony\Component\Yaml\Ya_1::class;
            const X = \Humbug\Symfony\Component\Yaml\Ya_1::class;
            const X = \Humbug\Symfony\Component\Yaml\Ya_1::class;

            PHP,
    ),
];
