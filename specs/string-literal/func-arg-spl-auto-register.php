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
        title: 'String literal used as a function argument of an spl_auto_register call',
    ),

    'FQCN string argument' => <<<'PHP'
        <?php

        spl_autoload_register('sodiumCompatAutoloader');
        spl_autoload_register('Sodium\compatAutoloader');
        spl_autoload_register(['Swift', 'autoload']);
        spl_autoload_register(['\Swift', 'autoload']);
        spl_autoload_register(['Humbug\\Swift', 'autoload']);
        spl_autoload_register(['\\Humbug\\Swift', 'autoload']);
        spl_autoload_register(['\\Humbug\\Swift', 'autoload']);
        spl_autoload_register(['DateTime', 'autoload']);
        spl_autoload_register(['\\DateTime', 'autoload']);

        ----
        <?php

        namespace Humbug;

        \spl_autoload_register('Humbug\\sodiumCompatAutoloader');
        \spl_autoload_register('Humbug\\Sodium\\compatAutoloader');
        \spl_autoload_register(['Humbug\\Swift', 'autoload']);
        \spl_autoload_register(['Humbug\\Swift', 'autoload']);
        \spl_autoload_register(['Humbug\\Swift', 'autoload']);
        \spl_autoload_register(['Humbug\\Swift', 'autoload']);
        \spl_autoload_register(['Humbug\\Swift', 'autoload']);
        \spl_autoload_register(['DateTime', 'autoload']);
        \spl_autoload_register(['\\DateTime', 'autoload']);

        PHP,

    'FQCN string argument on exposed class' => SpecWithConfig::create(
        exposeClasses: ['Symfony\Component\Yaml\Yaml', 'Swift'],
        spec: <<<'PHP'
            <?php

            spl_autoload_register(['Swift', 'autoload']);
            spl_autoload_register(['Humbug\\Swift', 'autoload']);
            spl_autoload_register(['\\Humbug\\Swift', 'autoload']);
            spl_autoload_register(['DateTime', 'autoload']);

            ----
            <?php

            namespace Humbug;

            \spl_autoload_register(['Humbug\\Swift', 'autoload']);
            \spl_autoload_register(['Humbug\\Swift', 'autoload']);
            \spl_autoload_register(['Humbug\\Swift', 'autoload']);
            \spl_autoload_register(['DateTime', 'autoload']);

            PHP,
    ),

    'FQCN string argument on exposed function' => SpecWithConfig::create(
        exposeFunctions: ['sodiumCompatAutoloader'],
        spec: <<<'PHP'
            <?php

            spl_autoload_register('sodiumCompatAutoloader');

            ----
            <?php

            namespace Humbug;

            \spl_autoload_register('Humbug\\sodiumCompatAutoloader');

            PHP,
    ),

    'FQCN string argument on class from an excluded namespace' => SpecWithConfig::create(
        excludeNamespaces: [
            'Symfony\Component\Yaml',
            '/^$/',
        ],
        spec: <<<'PHP'
            <?php

            spl_autoload_register(['Swift', 'autoload']);
            spl_autoload_register(['Humbug\\Swift', 'autoload']);
            spl_autoload_register(['\\Humbug\\Swift', 'autoload']);
            spl_autoload_register(['DateTime', 'autoload']);

            ----
            <?php

            namespace {
                \spl_autoload_register(['Swift', 'autoload']);
                \spl_autoload_register(['Humbug\\Swift', 'autoload']);
                \spl_autoload_register(['Humbug\\Swift', 'autoload']);
                \spl_autoload_register(['DateTime', 'autoload']);
            }

            PHP,
    ),

    'FQCN string argument on function from an excluded namespace' => SpecWithConfig::create(
        excludeNamespaces: [
            'Sodium',
            '/^$/',
        ],
        spec: <<<'PHP'
            <?php

            spl_autoload_register('Sodium\CompatAutoloader');

            ----
            <?php

            namespace {
                \spl_autoload_register('Sodium\\CompatAutoloader');
            }

            PHP,
    ),

    'FQCN string argument with global functions not exposed' => SpecWithConfig::create(
        exposeGlobalFunctions: false,
        spec: <<<'PHP'
            <?php

            spl_autoload_register(['Swift', 'autoload']);
            spl_autoload_register(['Humbug\\Swift', 'autoload']);
            spl_autoload_register(['\\Humbug\\Swift', 'autoload']);
            spl_autoload_register(['DateTime', 'autoload']);

            ----
            <?php

            namespace Humbug;

            \spl_autoload_register(['Humbug\\Swift', 'autoload']);
            \spl_autoload_register(['Humbug\\Swift', 'autoload']);
            \spl_autoload_register(['Humbug\\Swift', 'autoload']);
            \spl_autoload_register(['DateTime', 'autoload']);

            PHP,
    ),

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
        <?php

        spl_autoload_register(['Swift'.'', 'autoload']);

        ----
        <?php

        namespace Humbug;

        \spl_autoload_register(['Swift' . '', 'autoload']);

        PHP,

    'FQC constant call' => <<<'PHP'
        <?php

        namespace Symfony\Component\Yaml {
            class Yaml {}
        }

        namespace {
            spl_autoload_register([\Swift::class, 'autoload']);
            spl_autoload_register([\Humbug\Swift::class, 'autoload']);
            spl_autoload_register([\DateTime::class, 'autoload']);
        }
        ----
        <?php

        namespace Humbug\Symfony\Component\Yaml;

        class Yaml
        {
        }
        namespace Humbug;

        \spl_autoload_register([\Humbug\Swift::class, 'autoload']);
        \spl_autoload_register([\Humbug\Swift::class, 'autoload']);
        \spl_autoload_register([\DateTime::class, 'autoload']);

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
                spl_autoload_register([Symfony\Component\Yaml\Ya_1::class, 'autoload']);
                spl_autoload_register([\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
                spl_autoload_register([Humbug\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
                spl_autoload_register([\Humbug\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
            }
            ----
            <?php

            namespace Humbug\Symfony\Component\Yaml;

            class Ya_1
            {
            }
            \class_alias('Humbug\\Symfony\\Component\\Yaml\\Ya_1', 'Symfony\\Component\\Yaml\\Ya_1', \false);
            namespace Humbug;

            \spl_autoload_register([\Humbug\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
            \spl_autoload_register([\Humbug\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
            \spl_autoload_register([\Humbug\Symfony\Component\Yaml\Ya_1::class, 'autoload']);
            \spl_autoload_register([\Humbug\Symfony\Component\Yaml\Ya_1::class, 'autoload']);

            PHP,
    ),
];
