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

return [
    'meta' => [
        'title' => 'Class constant call in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

        'expose-global-constants' => false,
        'expose-global-classes' => false,
        'expose-global-functions' => false,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'expected-recorded-classes' => [],
        'expected-recorded-functions' => [],
    ],

    'Constant call on a class belonging to the global namespace' => <<<'PHP'
    <?php
    
    class Command {}
    
    Command::MAIN_CONST;
    ----
    <?php
    
    namespace Humbug;
    
    class Command
    {
    }
    Command::MAIN_CONST;
    
    PHP,

    'Constant call on a class belonging to the global namespace which is excluded' => [
        'exclude-namespaces' => ['/^$/'],
        'expected-recorded-classes' => [
            ['Command', 'Humbug\Command'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        class Command {}
        
        Command::MAIN_CONST;
        ----
        <?php
        
        namespace {
            class Command
            {
            }
            \class_alias('Humbug\\Command', 'Command', \false);
            \Command::MAIN_CONST;
        }
        
        PHP,
    ],

    'FQ constant call on a class belonging to the global namespace' => <<<'PHP'
    <?php
    
    class Command {}
    
    \Command::MAIN_CONST;
    ----
    <?php
    
    namespace Humbug;
    
    class Command
    {
    }
    \Humbug\Command::MAIN_CONST;
    
    PHP,

    'Constant call on an internal class belonging to the global namespace' => <<<'PHP'
    <?php
    
    Reflector::MAIN_CONST;
    ----
    <?php
    
    namespace Humbug;
    
    \Reflector::MAIN_CONST;
    
    PHP,

    'FQ constant call on an internal class belonging to the global namespace' => <<<'PHP'
    <?php
    
    \Reflector::MAIN_CONST;
    ----
    <?php
    
    namespace Humbug;
    
    \Reflector::MAIN_CONST;
    
    PHP,

    'Constant call on an exposed class belonging to the global namespace' => [
        'expose-classes' => ['Foo'],
        'payload' => <<<'PHP'
        <?php
        
        Foo::MAIN_CONST;
        ----
        <?php
        
        namespace Humbug;
        
        \Humbug\Foo::MAIN_CONST;
        
        PHP,
    ],

    'FQ constant call on an exposed class belonging to the global namespace' => [
        'expose-classes' => ['Foo'],
        'payload' => <<<'PHP'
        <?php
        
        \Foo::MAIN_CONST;
        ----
        <?php
        
        namespace Humbug;
        
        \Humbug\Foo::MAIN_CONST;
        
        PHP,
    ],
];
