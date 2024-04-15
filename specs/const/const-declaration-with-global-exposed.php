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
        title: 'Global constant declaration & usage in the global scope with the global constants exposed',
        exposeGlobalConstants: true,
    ),

    'Constants declaration in the global namespace' => <<<'PHP'
        <?php

        const FOO_CONST = foo();
        define('BAR_CONST', foo());
        define('Acme\BAR_CONST', foo());
        define(FOO_CONST, foo());
        define(\FOO_CONST, foo());
        define(\Acme\BAR_CONST, foo());
        ----
        <?php

        namespace Humbug;

        \define('FOO_CONST', foo());
        \define('BAR_CONST', foo());
        \define('Humbug\\Acme\\BAR_CONST', foo());
        \define(\FOO_CONST, foo());
        \define(\FOO_CONST, foo());
        \define(\Humbug\Acme\BAR_CONST, foo());

        PHP,

    'Constants declaration in the global namespace which is excluded' => SpecWithConfig::create(
        excludeNamespaces: [''],
        spec: <<<'PHP'
            <?php

            const FOO_CONST = foo();
            define('BAR_CONST', foo());
            define('Acme\BAR_CONST', foo());
            define(FOO_CONST, foo());
            define(\FOO_CONST, foo());
            define(\Acme\BAR_CONST, foo());
            ----
            <?php

            namespace {
                const FOO_CONST = \foo();
                \define('BAR_CONST', \foo());
                \define('Acme\\BAR_CONST', \foo());
                \define(\FOO_CONST, \foo());
                \define(\FOO_CONST, \foo());
                \define(\Acme\BAR_CONST, \foo());
            }

            PHP,
    ),

    'Exposed constants declaration in the global namespace' => SpecWithConfig::create(
        exposeConstants: [
            'FOO_CONST',
            'BAR_CONST',
            'Acme\BAR_CONST',
        ],
        spec: <<<'PHP'
            <?php

            const FOO_CONST = foo();
            define('BAR_CONST', foo());
            define('Acme\BAR_CONST', foo());
            define(FOO_CONST, foo());
            define(\FOO_CONST, foo());
            define(\Acme\BAR_CONST, foo());
            ----
            <?php

            namespace Humbug;

            \define('FOO_CONST', foo());
            \define('BAR_CONST', foo());
            \define('Acme\\BAR_CONST', foo());
            \define(\FOO_CONST, foo());
            \define(\FOO_CONST, foo());
            \define(\Acme\BAR_CONST, foo());

            PHP,
    ),

    'Excluded constants declaration in the global namespace' => SpecWithConfig::create(
        excludeConstants: [
            'FOO_CONST',
            'BAR_CONST',
            'Acme\BAR_CONST',
        ],
        spec: <<<'PHP'
            <?php

            const FOO_CONST = foo();
            define('BAR_CONST', foo());
            define('Acme\BAR_CONST', foo());
            define(FOO_CONST, foo());
            define(\FOO_CONST, foo());
            define(\Acme\BAR_CONST, foo());
            ----
            <?php

            namespace Humbug;

            \define('FOO_CONST', foo());
            \define('BAR_CONST', foo());
            \define('Acme\\BAR_CONST', foo());
            \define(\FOO_CONST, foo());
            \define(\FOO_CONST, foo());
            \define(\Acme\BAR_CONST, foo());

            PHP,
    ),

    'Constants declaration in a namespace' => SpecWithConfig::create(
        expectedRecordedAmbiguousFunctions: [
            ['define', 'Humbug\define'],
            ['foo', 'Humbug\foo'],
        ],
        spec: <<<'PHP'
            <?php

            namespace Acme;

            const FOO_CONST = foo();
            define('BAR_CONST', foo());
            define('Acme\BAR_CONST', foo());
            define(FOO_CONST, foo());
            define(\FOO_CONST, foo());
            define(\Acme\FOO_CONST, foo());
            ----
            <?php

            namespace Humbug\Acme;

            const FOO_CONST = foo();
            define('BAR_CONST', foo());
            define('Humbug\\Acme\\BAR_CONST', foo());
            define(FOO_CONST, foo());
            define(\FOO_CONST, foo());
            define(\Humbug\Acme\FOO_CONST, foo());

            PHP,
    ),

    'Constants declaration in an excluded namespace' => SpecWithConfig::create(
        excludeNamespaces: ['Acme'],
        expectedRecordedAmbiguousFunctions: [
            ['define', 'Humbug\define'],
            ['foo', 'Humbug\foo'],
        ],
        spec: <<<'PHP'
            <?php

            namespace Acme;

            const FOO_CONST = foo();
            define('BAR_CONST', foo());
            define('Acme\BAR_CONST', foo());
            define(FOO_CONST, foo());
            define(\FOO_CONST, foo());
            define(\Acme\BAR_CONST, foo());
            ----
            <?php

            namespace Acme;

            const FOO_CONST = foo();
            define('BAR_CONST', foo());
            define('Acme\\BAR_CONST', foo());
            define(FOO_CONST, foo());
            define(\FOO_CONST, foo());
            define(\Acme\BAR_CONST, foo());

            PHP,
    ),

    'Exposed constants declaration in a namespace' => SpecWithConfig::create(
        exposeConstants: ['Acme\BAR_CONST'],
        expectedRecordedAmbiguousFunctions: [
            ['define', 'Humbug\define'],
            ['foo', 'Humbug\foo'],
        ],
        spec: <<<'PHP'
            <?php

            namespace Acme;

            const FOO_CONST = foo();
            define('BAR_CONST', foo());
            define('Acme\BAR_CONST', foo());
            define(FOO_CONST, foo());
            define(\FOO_CONST, foo());
            define(\Acme\BAR_CONST, foo());
            ----
            <?php

            namespace Humbug\Acme;

            const FOO_CONST = foo();
            define('BAR_CONST', foo());
            define('Acme\\BAR_CONST', foo());
            define(FOO_CONST, foo());
            define(\FOO_CONST, foo());
            define(\Acme\BAR_CONST, foo());

            PHP,
    ),

    'Exposed constants declaration in an exposed namespace' => SpecWithConfig::create(
        exposeNamespaces: ['Acme'],
        spec: <<<'PHP'
            <?php

            namespace Acme;

            const FOO_CONST = foo();
            define('BAR_CONST', foo());
            define('Acme\BAR_CONST', foo());
            define(FOO_CONST, foo());
            define(\FOO_CONST, foo());
            define(\Acme\BAR_CONST, foo());
            ----
            <?php

            namespace Humbug\Acme;

            \define('Acme\\FOO_CONST', foo());
            define('BAR_CONST', foo());
            define('Acme\\BAR_CONST', foo());
            define(FOO_CONST, foo());
            define(\FOO_CONST, foo());
            define(\Acme\BAR_CONST, foo());

            PHP,
    ),
];
