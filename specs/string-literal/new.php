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
        title: 'String literal used as a new statement argument',
    ),

    'FQCN string argument' => <<<'PHP'
        <?php

        new X('Yaml', ['Yaml']);
        new X('\\Yaml', ['\\Yaml']);
        new X('Closure', ['Closure']);
        new X('\\Closure', ['\\Closure']);
        new X('Symfony\\Component\\Yaml\\Ya_1', ['Symfony\\Component\\Yaml\\Ya_1']);
        new X('\\Symfony\\Component\\Yaml\\Ya_1', ['\\Symfony\\Component\\Yaml\\Ya_1']);
        new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
        new X('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['\\Humbug\\Symfony\\Component\\Yaml\\Ya_1']);

        ----
        <?php

        namespace Humbug;

        new X('Yaml', ['Yaml']);
        new X('\Yaml', ['\Yaml']);
        new X('Closure', ['Closure']);
        new X('\Closure', ['\Closure']);
        new X('Humbug\Symfony\Component\Yaml\Ya_1', ['Humbug\Symfony\Component\Yaml\Ya_1']);
        new X('Humbug\Symfony\Component\Yaml\Ya_1', ['Humbug\Symfony\Component\Yaml\Ya_1']);
        new X('Humbug\Symfony\Component\Yaml\Ya_1', ['Humbug\Symfony\Component\Yaml\Ya_1']);
        new X('Humbug\Symfony\Component\Yaml\Ya_1', ['Humbug\Symfony\Component\Yaml\Ya_1']);

        PHP,

    'FQCN string argument on exposed class' => SpecWithConfig::create(
        exposeClasses: ['Symfony\Component\Yaml\Yaml'],
        spec: <<<'PHP'
            <?php

            new X('Symfony\\Component\\Yaml\\Ya_1l', ['Symfony\\Component\\Yaml\\Ya_1l']);
            new X('Symfony\\Component\\Yaml\\Ya_1', ['Symfony\\Component\\Yaml\\Ya_1']);
            new X('\\Symfony\\Component\\Yaml\\Ya_1', ['\\Symfony\\Component\\Yaml\\Ya_1']);
            new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
            new X('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['\\Humbug\\Symfony\\Component\\Yaml\\Ya_1']);

            ----
            <?php

            namespace Humbug;

            new X('Humbug\Symfony\Component\Yaml\Ya_1l', ['Humbug\Symfony\Component\Yaml\Ya_1l']);
            new X('Humbug\Symfony\Component\Yaml\Ya_1', ['Humbug\Symfony\Component\Yaml\Ya_1']);
            new X('Humbug\Symfony\Component\Yaml\Ya_1', ['Humbug\Symfony\Component\Yaml\Ya_1']);
            new X('Humbug\Symfony\Component\Yaml\Ya_1', ['Humbug\Symfony\Component\Yaml\Ya_1']);
            new X('Humbug\Symfony\Component\Yaml\Ya_1', ['Humbug\Symfony\Component\Yaml\Ya_1']);

            PHP,
    ),

    'FQCN string argument on classes belonging to an excluded namespace' => SpecWithConfig::create(
        excludeNamespaces: ['Symfony\Component'],
        spec: <<<'PHP'
            <?php

            new X('Symfony\\Yaml', ['Symfony\\Yaml']);
            new X('Symfony\\Component\\Yaml\\Ya_1', ['Symfony\\Component\\Yaml\\Ya_1']);
            new X('\\Symfony\\Component\\Yaml\\Ya_1', ['\\Symfony\\Component\\Yaml\\Ya_1']);
            new X('Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['Humbug\\Symfony\\Component\\Yaml\\Ya_1']);
            new X('\\Humbug\\Symfony\\Component\\Yaml\\Ya_1', ['\\Humbug\\Symfony\\Component\\Yaml\\Ya_1']);

            ----
            <?php

            namespace Humbug;

            new X('Humbug\Symfony\Yaml', ['Humbug\Symfony\Yaml']);
            new X('Symfony\Component\Yaml\Ya_1', ['Symfony\Component\Yaml\Ya_1']);
            new X('\Symfony\Component\Yaml\Ya_1', ['\Symfony\Component\Yaml\Ya_1']);
            new X('Humbug\Symfony\Component\Yaml\Ya_1', ['Humbug\Symfony\Component\Yaml\Ya_1']);
            new X('\Humbug\Symfony\Component\Yaml\Ya_1', ['\Humbug\Symfony\Component\Yaml\Ya_1']);

            PHP,
    ),

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
        <?php

        new X('Symfony\\Component' . '\\Yaml\\Ya_1', ['Symfony\\Component' . '\\Yaml\\Ya_1']);
        new X('\\Symfony\\Component' . '\\Yaml\\Ya_1', ['\\Symfony\\Component' . '\\Yaml\\Ya_1']);

        ----
        <?php

        namespace Humbug;

        new X('Symfony\Component' . '\Yaml\Ya_1', ['Symfony\Component' . '\Yaml\Ya_1']);
        new X('\Symfony\Component' . '\Yaml\Ya_1', ['\Symfony\Component' . '\Yaml\Ya_1']);

        PHP,

    'FQC constant call' => <<<'PHP'
        <?php

        namespace Symfony\Component\Yaml {
            class Yaml {}
        }

        namespace {
            new X(Symfony\Component\Yaml\Yaml::class, [Symfony\Component\Yaml\Yaml::class]);
            new X(\Symfony\Component\Yaml\Yaml::class, [\Symfony\Component\Yaml\Yaml::class]);
            new X(Humbug\Symfony\Component\Yaml\Yaml::class, [Humbug\Symfony\Component\Yaml\Yaml::class]);
            new X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
        }
        ----
        <?php

        namespace Humbug\Symfony\Component\Yaml;

        class Yaml
        {
        }
        namespace Humbug;

        new X(Symfony\Component\Yaml\Yaml::class, [Symfony\Component\Yaml\Yaml::class]);
        new X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
        new X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);
        new X(\Humbug\Symfony\Component\Yaml\Yaml::class, [\Humbug\Symfony\Component\Yaml\Yaml::class]);

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
                new X(Symfony\Component\Yaml\Ya_1::class, [Symfony\Component\Yaml\Ya_1::class]);
                new X(\Symfony\Component\Yaml\Ya_1::class, [\Symfony\Component\Yaml\Ya_1::class]);
                new X(Humbug\Symfony\Component\Yaml\Ya_1::class, [Humbug\Symfony\Component\Yaml\Ya_1::class]);
                new X(\Humbug\Symfony\Component\Yaml\Ya_1::class, [\Humbug\Symfony\Component\Yaml\Ya_1::class]);
            }
            ----
            <?php

            namespace Humbug\Symfony\Component\Yaml;

            class Ya_1
            {
            }
            \class_alias('Humbug\Symfony\Component\Yaml\Ya_1', 'Symfony\Component\Yaml\Ya_1', \false);
            namespace Humbug;

            new X(\Humbug\Symfony\Component\Yaml\Ya_1::class, [\Humbug\Symfony\Component\Yaml\Ya_1::class]);
            new X(\Humbug\Symfony\Component\Yaml\Ya_1::class, [\Humbug\Symfony\Component\Yaml\Ya_1::class]);
            new X(\Humbug\Symfony\Component\Yaml\Ya_1::class, [\Humbug\Symfony\Component\Yaml\Ya_1::class]);
            new X(\Humbug\Symfony\Component\Yaml\Ya_1::class, [\Humbug\Symfony\Component\Yaml\Ya_1::class]);

            PHP,
    ),

    'new parent is a class name' => <<<'PHP'
        <?php
        namespace Acme;

        class BplaaYai {}

        new BplaaYai('abc');

        ----
        <?php

        namespace Humbug\Acme;

        class BplaaYai
        {
        }
        new BplaaYai('abc');

        PHP,

    'new parent is a variable' => <<<'PHP'
        <?php
        namespace Acme;

        class BplaaYai {}

        $class = '\Acme\BplaaYai';
        new $class('abc');

        ----
        <?php

        namespace Humbug\Acme;

        class BplaaYai
        {
        }
        $class = 'Humbug\Acme\BplaaYai';
        new $class('abc');

        PHP,

    'new parent is an expression (variable)' => <<<'PHP'
        <?php
        namespace Acme;

        class BplaaYai {}

        $class = '\Acme\BplaaYai';
        new $class('abc');

        ----
        <?php

        namespace Humbug\Acme;

        class BplaaYai
        {
        }
        $class = 'Humbug\Acme\BplaaYai';
        new $class('abc');

        PHP,

    'new parent is an anonymous class' => <<<'PHP'
        <?php
        namespace Acme;

        class BplaaYai {}

        new class('abc') {};

        ----
        <?php

        namespace Humbug\Acme;

        class BplaaYai
        {
        }
        new class('abc')
        {
        };

        PHP,
];
