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
        title: 'String literal assigned to a variable',
    ),

    'FQCN string argument' => <<<'PHP'
        <?php

        $x = 'Yaml';
        $x = '\\Yaml';
        $x = 'Closure';
        $x = '\\Closure';
        $x = 'Symfony\\Component\\Yaml\\Ya_1';
        $x = '\\Symfony\\Component\\Yaml\\Ya_1';
        $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        $x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';
        $x = '1\2';

        ----
        <?php

        namespace Humbug;

        $x = 'Yaml';
        $x = '\Yaml';
        $x = 'Closure';
        $x = '\Closure';
        $x = 'Humbug\Symfony\Component\Yaml\Ya_1';
        $x = 'Humbug\Symfony\Component\Yaml\Ya_1';
        $x = 'Humbug\Symfony\Component\Yaml\Ya_1';
        $x = 'Humbug\Symfony\Component\Yaml\Ya_1';
        $x = '1\2';

        PHP,

    'Invalid FQCN strings' => <<<'PHP'
        <?php

        $regex = '%if \(defined\(\$name = \'PhpParser\\\\\\\\Parser\\\\\\\\Tokens%';
        $shortcuts = preg_split('{(\|)-?}', ltrim($shortcut, '-'));

        ----
        <?php

        namespace Humbug;

        $regex = '%if \(defined\(\$name = \'PhpParser\\\\\\\\Parser\\\\\\\\Tokens%';
        $shortcuts = \preg_split('{(\|)-?}', \ltrim($shortcut, '-'));

        PHP,

    'FQCN string argument on exposed class' => SpecWithConfig::create(
        exposeClasses: ['Symfony\Component\Yaml\Yaml'],
        spec: <<<'PHP'
            <?php

            $x = 'Symfony\\Component\\Yaml\\Ya_1l';
            $x = 'Symfony\\Component\\Yaml\\Ya_1';
            $x = '\\Symfony\\Component\\Yaml\\Ya_1';
            $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
            $x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';

            ----
            <?php

            namespace Humbug;

            $x = 'Humbug\Symfony\Component\Yaml\Ya_1l';
            $x = 'Humbug\Symfony\Component\Yaml\Ya_1';
            $x = 'Humbug\Symfony\Component\Yaml\Ya_1';
            $x = 'Humbug\Symfony\Component\Yaml\Ya_1';
            $x = 'Humbug\Symfony\Component\Yaml\Ya_1';

            PHP,
    ),

    'FQCN string argument on classes belonging to an excluded namespace' => SpecWithConfig::create(
        excludeNamespaces: ['Symfony\Component'],
        spec: <<<'PHP'
            <?php

            $x = 'Symfony\\Yaml';
            $x = 'Symfony\\Component\\Yaml\\Ya_1';
            $x = '\\Symfony\\Component\\Yaml\\Ya_1';
            $x = 'Humbug\\Symfony\\Component\\Yaml\\Ya_1';
            $x = '\\Humbug\\Symfony\\Component\\Yaml\\Ya_1';

            ----
            <?php

            namespace Humbug;

            $x = 'Humbug\Symfony\Yaml';
            $x = 'Symfony\Component\Yaml\Ya_1';
            $x = '\Symfony\Component\Yaml\Ya_1';
            $x = 'Humbug\Symfony\Component\Yaml\Ya_1';
            $x = '\Humbug\Symfony\Component\Yaml\Ya_1';

            PHP,
    ),

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
        <?php

        $x = 'Symfony\\Component' . '\\Yaml\\Ya_1';
        $x = '\\Symfony\\Component' . '\\Yaml\\Ya_1';

        ----
        <?php

        namespace Humbug;

        $x = 'Symfony\Component' . '\Yaml\Ya_1';
        $x = '\Symfony\Component' . '\Yaml\Ya_1';

        PHP,

    'FQC constant call' => <<<'PHP'
        <?php

        namespace Symfony\Component\Yaml {
            class Yaml {}
        }

        namespace {
            $x = Symfony\Component\Yaml\Yaml::class;
            $x = \Symfony\Component\Yaml\Yaml::class;
            $x = Humbug\Symfony\Component\Yaml\Yaml::class;
            $x = \Humbug\Symfony\Component\Yaml\Yaml::class;
        }
        ----
        <?php

        namespace Humbug\Symfony\Component\Yaml;

        class Yaml
        {
        }
        namespace Humbug;

        $x = Symfony\Component\Yaml\Yaml::class;
        $x = \Humbug\Symfony\Component\Yaml\Yaml::class;
        $x = \Humbug\Symfony\Component\Yaml\Yaml::class;
        $x = \Humbug\Symfony\Component\Yaml\Yaml::class;

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
                $x = Symfony\Component\Yaml\Ya_1::class;
                $x = \Symfony\Component\Yaml\Ya_1::class;
                $x = Humbug\Symfony\Component\Yaml\Ya_1::class;
                $x = \Humbug\Symfony\Component\Yaml\Ya_1::class;
            }
            ----
            <?php

            namespace Humbug\Symfony\Component\Yaml;

            class Ya_1
            {
            }
            \class_alias('Humbug\Symfony\Component\Yaml\Ya_1', 'Symfony\Component\Yaml\Ya_1', \false);
            namespace Humbug;

            $x = \Humbug\Symfony\Component\Yaml\Ya_1::class;
            $x = \Humbug\Symfony\Component\Yaml\Ya_1::class;
            $x = \Humbug\Symfony\Component\Yaml\Ya_1::class;
            $x = \Humbug\Symfony\Component\Yaml\Ya_1::class;

            PHP,
    ),
];
