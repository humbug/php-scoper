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

    'Constants declaration in the global namespace which is excluded' => [
        excludeNamespaces: [''],
        'payload' => <<<'PHP'
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

    'Constants declaration in the global namespace which is excluded' => [
        'exclude-namespaces' => [''],
        'payload' => <<<'PHP'
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
    ],

    'Exposed constants declaration in the global namespace' => [
        exposeConstants: [
            'FOO_CONST',
            'BAR_CONST',
            'Acme\BAR_CONST',
        ],
        'payload' => <<<'PHP'
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
    ],

    'Excluded constants declaration in the global namespace' => [
        excludeConstants: [
            'FOO_CONST',
            'BAR_CONST',
            'Acme\BAR_CONST',
        ],
        'payload' => <<<'PHP'
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
    ],

    'Constants declaration in a namespace' => <<<'PHP'
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
        \define('BAR_CONST', foo());
        \define('Humbug\\Acme\\BAR_CONST', foo());
        \define(FOO_CONST, foo());
        \define(\FOO_CONST, foo());
        \define(\Humbug\Acme\FOO_CONST, foo());

        PHP,

    'Constants declaration in an excluded namespace' => [
        excludeNamespaces: ['Acme'],
        'payload' => <<<'PHP'
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
            \define('BAR_CONST', foo());
            \define('Acme\\BAR_CONST', foo());
            \define(FOO_CONST, foo());
            \define(\FOO_CONST, foo());
            \define(\Acme\BAR_CONST, foo());

            PHP,
    ],

    'Exposed constants declaration in a namespace' => [
        exposeConstants: ['Acme\BAR_CONST'],
        'payload' => <<<'PHP'
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
            \define('BAR_CONST', foo());
            \define('Acme\\BAR_CONST', foo());
            \define(FOO_CONST, foo());
            \define(\FOO_CONST, foo());
            \define(\Acme\BAR_CONST, foo());

            PHP,
    ],

    'Exposed constants declaration in an exposed namespace' => [
        exposeNamespaces: ['Acme'],
        'payload' => <<<'PHP'
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
            \define('BAR_CONST', foo());
            \define('Acme\\BAR_CONST', foo());
            \define(FOO_CONST, foo());
            \define(\FOO_CONST, foo());
            \define(\Acme\BAR_CONST, foo());

            PHP,
    ],
];
