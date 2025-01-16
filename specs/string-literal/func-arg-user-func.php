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
        title: 'String literal used as a function argument of a user-defined function',
    ),

    'FQCN string argument' => <<<'PHP'
        <?php

        foo('Symfony\\Component\\Yaml\\Ya_1');
        foo('\\Symfony\\Component\\Yaml\\Ya_1');
        foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
        foo('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1');

        foo('DateTime');
        foo('\\DateTime');
        foo('Swift');
        foo('\\Swift');

        foo(['DateTime', 'autoload']);
        foo(['\\DateTime', 'autoload']);
        foo(['Swift', 'autoload']);
        foo(['\\Swift', 'autoload']);

        ----
        <?php

        namespace Humbug;

        foo('Humbug\Symfony\Component\Yaml\Ya_1');
        foo('Humbug\Symfony\Component\Yaml\Ya_1');
        foo('Humbug\Symfony\Component\Yaml\Ya_1');
        foo('Humbug\Symfony\Component\Yaml\Ya_1');
        foo('DateTime');
        foo('\DateTime');
        foo('Swift');
        foo('\Swift');
        foo(['DateTime', 'autoload']);
        foo(['\DateTime', 'autoload']);
        foo(['Swift', 'autoload']);
        foo(['\Swift', 'autoload']);

        PHP,

    'FQCN string argument on exposed class' => SpecWithConfig::create(
        exposeClasses: ['Symfony\Component\Yaml\Yaml', 'Swift'],
        spec: <<<'PHP'
            <?php

            foo('Symfony\\Component\\Yaml\\Ya_1');
            foo('\\Symfony\\Component\\Yaml\\Ya_1');
            foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
            foo('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1');

            foo('DateTime');
            foo('Swift');
            foo(['DateTime', 'autoload']);
            foo(['Swift', 'autoload']);

            ----
            <?php

            namespace Humbug;

            foo('Humbug\Symfony\Component\Yaml\Ya_1');
            foo('Humbug\Symfony\Component\Yaml\Ya_1');
            foo('Humbug\Symfony\Component\Yaml\Ya_1');
            foo('Humbug\Symfony\Component\Yaml\Ya_1');
            foo('DateTime');
            foo('Swift');
            foo(['DateTime', 'autoload']);
            foo(['Swift', 'autoload']);

            PHP,
    ),

    'FQCN string argument on class from global namespace with classes from global namespace exposed' => SpecWithConfig::create(
        exposeGlobalClasses: true,
        spec: <<<'PHP'
            <?php

            foo('DateTime');
            foo('Swift');
            foo(['DateTime', 'autoload']);
            foo(['Swift', 'autoload']);

            ----
            <?php

            namespace Humbug;

            foo('DateTime');
            foo('Swift');
            foo(['DateTime', 'autoload']);
            foo(['Swift', 'autoload']);

            PHP,
    ),

    'FQCN string argument on class from an excluded namespace' => SpecWithConfig::create(
        excludeNamespaces: [
            'Symfony\Component\Yaml',
            '/^$/',
        ],
        spec: <<<'PHP'
            <?php

            foo('Symfony\\Component\\Yaml\\Ya_1');
            foo('\\Symfony\\Component\\Yaml\\Ya_1');
            foo('Humbug\\Symfony\\Component\\Yaml\\Ya_1');
            foo('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1');

            foo('DateTime');
            foo('Swift');
            foo(['DateTime', 'autoload']);
            foo(['Swift', 'autoload']);

            ----
            <?php

            namespace {
                \foo('Symfony\Component\Yaml\Ya_1');
                \foo('\Symfony\Component\Yaml\Ya_1');
                \foo('Humbug\Symfony\Component\Yaml\Ya_1');
                \foo('\Humbug\Symfony\Component\Yaml\Ya_1');
                \foo('DateTime');
                \foo('Swift');
                \foo(['DateTime', 'autoload']);
                \foo(['Swift', 'autoload']);
            }

            PHP,
    ),

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
        <?php

        foo('Symfony\\Component' . '\\Yaml\\Ya_1');
        foo('\\Symfony\\Component' . '\\Yaml\\Ya_1');

        foo('Swift'.'');

        ----
        <?php

        namespace Humbug;

        foo('Symfony\Component' . '\Yaml\Ya_1');
        foo('\Symfony\Component' . '\Yaml\Ya_1');
        foo('Swift' . '');

        PHP,

    'FQC class constant call' => <<<'PHP'
        <?php

        namespace Symfony\Component\Yaml {
            class Yaml {}
        }

        namespace {
            foo(Symfony\Component\Yaml\Yaml::class);
            foo(\Symfony\Component\Yaml\Yaml::class);
            foo(Humbug\Symfony\Component\Yaml\Yaml::class);
            foo(\Humbug\Symfony\Component\Yaml\Yaml::class);

            foo(\DateTime::class);
            foo(\Swift::class);
            foo([\DateTime::class, 'autoload']);
            foo([\Swift::class, 'autoload']);
        }
        ----
        <?php

        namespace Humbug\Symfony\Component\Yaml;

        class Yaml
        {
        }
        namespace Humbug;

        foo(Symfony\Component\Yaml\Yaml::class);
        foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
        foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
        foo(\Humbug\Symfony\Component\Yaml\Yaml::class);
        foo(\DateTime::class);
        foo(\Humbug\Swift::class);
        foo([\DateTime::class, 'autoload']);
        foo([\Humbug\Swift::class, 'autoload']);

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
                foo(Symfony\Component\Yaml\Ya_1::class);
                foo(\Symfony\Component\Yaml\Ya_1::class);
                foo(Humbug\Symfony\Component\Yaml\Ya_1::class);
                foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
            }
            ----
            <?php

            namespace Humbug\Symfony\Component\Yaml;

            class Ya_1
            {
            }
            \class_alias('Humbug\Symfony\Component\Yaml\Ya_1', 'Symfony\Component\Yaml\Ya_1', \false);
            namespace Humbug;

            foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
            foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
            foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);
            foo(\Humbug\Symfony\Component\Yaml\Ya_1::class);

            PHP,
    ),
];
