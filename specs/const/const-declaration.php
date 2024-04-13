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
        title: 'Global constant declaration in the global scope',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'Constants declaration in the global namespace' => <<<'PHP'
    <?php
    
    const FOO_CONST = foo();
    const X = 'x', Y = '';
    if (!defined('BAR_CONST')) {
        define('BAR_CONST', foo());
    }
    if (!defined('Acme\BAR_CONST')) {
        define('Acme\BAR_CONST', foo());
    }
    if (!defined('FOO_CONST')) {
        define(FOO_CONST, foo());
    }
    if (!defined('FOO_CONST')) {
        define(\FOO_CONST, foo());
    }
    if (!defined('Acme\BAR_CONST')) {
        define(\Acme\BAR_CONST, foo());
    }
    const PHP_VERSION = 81400;
    ----
    <?php
    
    namespace Humbug;
    
    const FOO_CONST = foo();
    if (\true) {
        const X = 'x';
        const Y = '';
    }
    if (!\defined('Humbug\\BAR_CONST')) {
        \define('Humbug\\BAR_CONST', foo());
    }
    if (!\defined('Humbug\\Acme\\BAR_CONST')) {
        \define('Humbug\\Acme\\BAR_CONST', foo());
    }
    if (!\defined('Humbug\\FOO_CONST')) {
        \define(\Humbug\FOO_CONST, foo());
    }
    if (!\defined('Humbug\\FOO_CONST')) {
        \define(\Humbug\FOO_CONST, foo());
    }
    if (!\defined('Humbug\\Acme\\BAR_CONST')) {
        \define(\Humbug\Acme\BAR_CONST, foo());
    }
    \define('PHP_VERSION', 81400);
    
    PHP,

    'Constant declarations in the global namespace which is excluded' => [
        excludeNamespaces: [''],
        'payload' => <<<'PHP'
        <?php

        const FOO_CONST = foo();
        const X = 'x', Y = '';
        if (!defined('BAR_CONST')) {
            define('BAR_CONST', foo());
        }
        if (!defined('Acme\BAR_CONST')) {
            define('Acme\BAR_CONST', foo());
        }
        if (!defined('FOO_CONST')) {
            define(FOO_CONST, foo());
        }
        if (!defined('FOO_CONST')) {
            define(\FOO_CONST, foo());
        }
        if (!defined('Acme\BAR_CONST')) {
            define(\Acme\BAR_CONST, foo());
        }
        const PHP_VERSION = 81400;
        ----
        <?php

        namespace Humbug;

        const FOO_CONST = foo();
        if (\true) {
            const X = 'x';
            const Y = '';
        }
        if (!\defined('Humbug\\BAR_CONST')) {
            \define('Humbug\\BAR_CONST', foo());
        }
        if (!\defined('Humbug\\Acme\\BAR_CONST')) {
            \define('Humbug\\Acme\\BAR_CONST', foo());
        }
        if (!\defined('Humbug\\FOO_CONST')) {
            \define(\Humbug\FOO_CONST, foo());
        }
        if (!\defined('Humbug\\FOO_CONST')) {
            \define(\Humbug\FOO_CONST, foo());
        }
        if (!\defined('Humbug\\Acme\\BAR_CONST')) {
            \define(\Humbug\Acme\BAR_CONST, foo());
        }
        \define('PHP_VERSION', 81400);

        PHP,

    'Constant declarations in the global namespace which is excluded' => [
        'exclude-namespaces' => [''],
        'payload' => <<<'PHP'
            <?php

            const FOO_CONST = foo();
            const X = 'x', Y = '';
            if (!defined('BAR_CONST')) {
                define('BAR_CONST', foo());
            }
            if (!defined('Acme\BAR_CONST')) {
                define('Acme\BAR_CONST', foo());
            }
            if (!defined('FOO_CONST')) {
                define(FOO_CONST, foo());
            }
            if (!defined('FOO_CONST')) {
                define(\FOO_CONST, foo());
            }
            if (!defined('Acme\BAR_CONST')) {
                define(\Acme\BAR_CONST, foo());
            }
            ----
            <?php

            namespace {
                const FOO_CONST = \foo();
                if (\true) {
                    const X = 'x';
                    const Y = '';
                }
                if (!\defined('BAR_CONST')) {
                    \define('BAR_CONST', \foo());
                }
                if (!\defined('Acme\\BAR_CONST')) {
                    \define('Acme\\BAR_CONST', \foo());
                }
                if (!\defined('FOO_CONST')) {
                    \define(\FOO_CONST, \foo());
                }
                if (!\defined('FOO_CONST')) {
                    \define(\FOO_CONST, \foo());
                }
                if (!\defined('Acme\\BAR_CONST')) {
                    \define(\Acme\BAR_CONST, \foo());
                }
            }

            PHP,
    ],

    'Exposed constant declarations in the global namespace' => [
        exposeConstants: [
            'FOO_CONST',
            'BAR_CONST',
            'Acme\BAR_CONST',
            'BAZ',
            'Emca\BAZ',
        ],
        'payload' => <<<'PHP'
            <?php

            const FOO_CONST = foo();
            const X = 'x', Y = '';
            if (!defined('BAR_CONST')) {
                define('BAR_CONST', foo());
            }
            if (!defined('Acme\BAR_CONST')) {
                define('Acme\BAR_CONST', foo());
            }
            if (!defined('FOO_CONST')) {
                define(FOO_CONST, foo());
            }
            if (!defined('FOO_CONST')) {
                define(\FOO_CONST, foo());
            }
            if (!defined('Acme\BAR_CONST')) {
                define(\Acme\BAR_CONST, foo());
            }

            if (!defined('BAZ')) {
                define('BAZ', 'baz');
            }
            if (!defined('Emca\BAZ')) {
                define('Emca\BAZ', 'baz');
            }
            ----
            <?php

            namespace Humbug;

            \define('FOO_CONST', foo());
            if (\true) {
                const X = 'x';
                const Y = '';
            }
            if (!\defined('BAR_CONST')) {
                \define('BAR_CONST', foo());
            }
            if (!\defined('Acme\\BAR_CONST')) {
                \define('Acme\\BAR_CONST', foo());
            }
            if (!\defined('FOO_CONST')) {
                \define(\FOO_CONST, foo());
            }
            if (!\defined('FOO_CONST')) {
                \define(\FOO_CONST, foo());
            }
            if (!\defined('Acme\\BAR_CONST')) {
                \define(\Acme\BAR_CONST, foo());
            }
            if (!\defined('BAZ')) {
                \define('BAZ', 'baz');
            }
            if (!\defined('Emca\\BAZ')) {
                \define('Emca\\BAZ', 'baz');
            }

            PHP,
    ],

    'Exposed grouped constants declaration in the global namespace' => [
        exposeConstants: ['X'],
        'payload' => <<<'PHP'
            <?php

            const X = 'x', Y = '';
            ----
            <?php

            namespace Humbug;

            if (\true) {
                \define('X', 'x');
                const Y = '';
            }

            PHP,
    ],

    'Constants declaration in a namespace' => <<<'PHP'
    <?php
    
    namespace Acme;
    
    const FOO_CONST = foo();
    const X = 'x', Y = '';
    
    if (!defined('BAR_CONST')) {
        define('BAR_CONST', foo());
    }
    if (!defined('Acme\BAR_CONST')) {
        define('Acme\BAR_CONST', foo());
    }
    if (!defined('Acme\FOO_CONST')) {
        define(FOO_CONST, foo());
    }
    if (!defined('FOO_CONST')) {
        define(\FOO_CONST, foo());
    }
    if (!defined('Acme\BAR_CONST')) {
        define(\Acme\BAR_CONST, foo());
    }
    ----
    <?php
    
    namespace Humbug\Acme;
    
    const FOO_CONST = foo();
    if (\true) {
        const X = 'x';
        const Y = '';
    }
    if (!\defined('Humbug\\BAR_CONST')) {
        \define('Humbug\\BAR_CONST', foo());
    }
    if (!\defined('Humbug\\Acme\\BAR_CONST')) {
        \define('Humbug\\Acme\\BAR_CONST', foo());
    }
    if (!\defined('Humbug\\Acme\\FOO_CONST')) {
        \define(FOO_CONST, foo());
    }
    if (!\defined('Humbug\\FOO_CONST')) {
        \define(\Humbug\FOO_CONST, foo());
    }
    if (!\defined('Humbug\\Acme\\BAR_CONST')) {
        \define(\Humbug\Acme\BAR_CONST, foo());
    }
    
    PHP,

    'Constants declaration in an excluded namespace' => [
        excludeNamespaces: ['Acme'],
        'payload' => <<<'PHP'
        <?php

        namespace Acme;

        const FOO_CONST = foo();
        const X = 'x', Y = '';

        if (!defined('BAR_CONST')) {
            define('BAR_CONST', foo());
        }
        if (!defined('Acme\BAR_CONST')) {
            define('Acme\BAR_CONST', foo());
        }
        if (!defined('Acme\FOO_CONST')) {
            define(FOO_CONST, foo());
        }
        if (!defined('FOO_CONST')) {
            define(\FOO_CONST, foo());
        }
        if (!defined('Acme\BAR_CONST')) {
            define(\Acme\BAR_CONST, foo());
        }
        ----
        <?php

    'Exposed constants declaration in a namespace' => [
        exposeConstants: ['Acme\BAR_CONST'],
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme;
        
        const FOO_CONST = foo();
        const X = 'x', Y = '';
        
        if (!defined('BAR_CONST')) {
            define('BAR_CONST', foo());
        }
        if (!defined('Acme\BAR_CONST')) {
            define('Acme\BAR_CONST', foo());
        }
        if (!defined('Acme\FOO_CONST')) {
            define(FOO_CONST, foo());
        }
        if (!defined('FOO_CONST')) {
            define(\FOO_CONST, foo());
        }
        if (!defined('Acme\BAR_CONST')) {
            define(\Acme\BAR_CONST, foo());
        }
        ----
        <?php
        
        namespace Humbug\Acme;

        const FOO_CONST = foo();
        if (\true) {
            const X = 'x';
            const Y = '';
        }
        if (!\defined('Humbug\\BAR_CONST')) {
            \define('Humbug\\BAR_CONST', foo());
        }
        if (!\defined('Humbug\\Acme\\BAR_CONST')) {
            \define('Humbug\\Acme\\BAR_CONST', foo());
        }
        if (!\defined('Humbug\\Acme\\FOO_CONST')) {
            \define(FOO_CONST, foo());
        }
        if (!\defined('Humbug\\FOO_CONST')) {
            \define(\Humbug\FOO_CONST, foo());
        }
        if (!\defined('Humbug\\Acme\\BAR_CONST')) {
            \define(\Humbug\Acme\BAR_CONST, foo());
        }

        PHP,

    'Constants declaration in an excluded namespace' => [
        'exclude-namespaces' => ['Acme'],
        'payload' => <<<'PHP'
            <?php

            namespace Acme;

            const FOO_CONST = foo();
            const X = 'x', Y = '';

            if (!defined('BAR_CONST')) {
                define('BAR_CONST', foo());
            }
            if (!defined('Acme\BAR_CONST')) {
                define('Acme\BAR_CONST', foo());
            }
            if (!defined('Acme\FOO_CONST')) {
                define(FOO_CONST, foo());
            }
            if (!defined('FOO_CONST')) {
                define(\FOO_CONST, foo());
            }
            if (!defined('Acme\BAR_CONST')) {
                define(\Acme\BAR_CONST, foo());
            }
            ----
            <?php

            namespace Acme;

            const FOO_CONST = foo();
            if (\true) {
                const X = 'x';
                const Y = '';
            }
            if (!\defined('Humbug\\BAR_CONST')) {
                \define('Humbug\\BAR_CONST', foo());
            }
            if (!\defined('Acme\\BAR_CONST')) {
                \define('Acme\\BAR_CONST', foo());
            }
            if (!\defined('Acme\\FOO_CONST')) {
                \define(FOO_CONST, foo());
            }
            if (!\defined('Humbug\\FOO_CONST')) {
                \define(\Humbug\FOO_CONST, foo());
            }
            if (!\defined('Acme\\BAR_CONST')) {
                \define(\Acme\BAR_CONST, foo());
            }

            PHP,
    ],

    'Exposed constants declaration in a namespace' => [
        'expose-constants' => ['Acme\BAR_CONST'],
        'payload' => <<<'PHP'
            <?php

            namespace Acme;

            const FOO_CONST = foo();
            const X = 'x', Y = '';

            if (!defined('BAR_CONST')) {
                define('BAR_CONST', foo());
            }
            if (!defined('Acme\BAR_CONST')) {
                define('Acme\BAR_CONST', foo());
            }
            if (!defined('Acme\FOO_CONST')) {
                define(FOO_CONST, foo());
            }
            if (!defined('FOO_CONST')) {
                define(\FOO_CONST, foo());
            }
            if (!defined('Acme\BAR_CONST')) {
                define(\Acme\BAR_CONST, foo());
            }
            ----
            <?php

            namespace Humbug\Acme;

            const FOO_CONST = foo();
            if (\true) {
                const X = 'x';
                const Y = '';
            }
            if (!\defined('Humbug\\BAR_CONST')) {
                \define('Humbug\\BAR_CONST', foo());
            }
            if (!\defined('Acme\\BAR_CONST')) {
                \define('Acme\\BAR_CONST', foo());
            }
            if (!\defined('Humbug\\Acme\\FOO_CONST')) {
                \define(FOO_CONST, foo());
            }
            if (!\defined('Humbug\\FOO_CONST')) {
                \define(\Humbug\FOO_CONST, foo());
            }
            if (!\defined('Acme\\BAR_CONST')) {
                \define(\Acme\BAR_CONST, foo());
            }

            PHP,
    ],

    'Token compatibility regression test' => [
        excludeConstants: [
            'NEW_TOKEN',
            'ANOTHER_NEW_TOKEN',
        ],
        'payload' => <<<'PHP'
            <?php

            namespace {
                const NEW_TOKEN = 501;
            }

            namespace PHPParser {
                class Lexer {
                    function isNewToken(int $token): bool {
                        return NEW_TOKEN === $token;
                    }

                    function isAnotherNewToken(int $token): bool {
                        if (!\defined('ANOTHER_NEW_TOKEN')) {
                            \define('ANOTHER_NEW_TOKEN', 502);
                        }

                        return ANOTHER_NEW_TOKEN === $token;
                    }
                }
            }

            namespace FQ_PHPParser {
                class Lexer {
                    function isNewToken(int $token): bool {
                        return \NEW_TOKEN === $token;
                    }

                    function isAnotherNewToken(int $token): bool {
                        if (!\defined('ANOTHER_NEW_TOKEN')) {
                            \define('ANOTHER_NEW_TOKEN', 502);
                        }

                        return \ANOTHER_NEW_TOKEN === $token;
                    }
                }
            }

            ----
            <?php

            namespace Humbug;

            \define('NEW_TOKEN', 501);
            namespace Humbug\PHPParser;

            class Lexer
            {
                function isNewToken(int $token) : bool
                {
                    return \NEW_TOKEN === $token;
                }
                function isAnotherNewToken(int $token) : bool
                {
                    if (!\defined('ANOTHER_NEW_TOKEN')) {
                        \define('ANOTHER_NEW_TOKEN', 502);
                    }
                    return \ANOTHER_NEW_TOKEN === $token;
                }
            }
            namespace Humbug\FQ_PHPParser;

            class Lexer
            {
                function isNewToken(int $token) : bool
                {
                    return \NEW_TOKEN === $token;
                }
                function isAnotherNewToken(int $token) : bool
                {
                    if (!\defined('ANOTHER_NEW_TOKEN')) {
                        \define('ANOTHER_NEW_TOKEN', 502);
                    }
                    return \ANOTHER_NEW_TOKEN === $token;
                }
            }

            PHP,
    ],

    'Define check of a global class constant' => <<<'PHP'
        <?php

        if (!defined('Bar::TEST')) {
        }
        if (!defined('\Bar::TEST')) {
        }
        if (!defined('\\Bar::TEST')) {
        }
        ----
        <?php

        namespace Humbug;

        if (!\defined('Humbug\\Bar::TEST')) {
        }
        if (!\defined('Humbug\\Bar::TEST')) {
        }
        if (!\defined('Humbug\\Bar::TEST')) {
        }

        PHP,

    'Define check of a namespaced class constant' => <<<'PHP'
        <?php

        if (!defined('Acme\Bar::TEST')) {
        }
        if (!defined('\Acme\Bar::TEST')) {
        }
        if (!defined('\\Acme\\Bar::TEST')) {
        }
        ----
        <?php

        namespace Humbug;

        if (!\defined('Humbug\\Acme\\Bar::TEST')) {
        }
        if (!\defined('Humbug\\Acme\\Bar::TEST')) {
        }
        if (!\defined('Humbug\\Acme\\Bar::TEST')) {
        }

        PHP,
];
