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
        'title' => 'Class static property call in the global scope',
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
    
    Command::$mainStaticProp;
    ----
    <?php
    
    namespace Humbug;
    
    class Command
    {
    }
    Command::$mainStaticProp;
    
    PHP,

    'Constant call on a class belonging to the global namespace which is excluded' => [
        'exclude-namespaces' => ['/^$/'],
        'payload' => <<<'PHP'
        <?php
        
        class Command {}
        
        Command::$mainStaticProp;
        ----
        <?php
        
        namespace {
            class Command
            {
            }
            \Command::$mainStaticProp;
        }
        
        PHP,
    ],

    'FQ constant call on a class belonging to the global namespace' => <<<'PHP'
    <?php
    
    class Command {}
    
    \Command::$mainStaticProp;
    ----
    <?php
    
    namespace Humbug;
    
    class Command
    {
    }
    \Humbug\Command::$mainStaticProp;
    
    PHP,

    'Constant call on an internal class belonging to the global namespace' => <<<'PHP'
    <?php
    
    Reflector::$mainStaticProp;
    ----
    <?php
    
    namespace Humbug;
    
    \Reflector::$mainStaticProp;
    
    PHP,

    'FQ constant call on an internal class belonging to the global namespace' => <<<'PHP'
    <?php
    
    \Reflector::$mainStaticProp;
    ----
    <?php
    
    namespace Humbug;
    
    \Reflector::$mainStaticProp;
    
    PHP,

    // TODO: this should not have been made into a FQC call
    'Constant call on an exposed class belonging to the global namespace' => [
        'expose-classes' => ['Foo'],
        'payload' => <<<'PHP'
        <?php
        
        Foo::$mainStaticProp;
        ----
        <?php
        
        namespace Humbug;
        
        \Humbug\Foo::$mainStaticProp;
        
        PHP,
    ],

    'FQ constant call on an exposed class belonging to the global namespace' => [
        'expose-classes' => ['Foo'],
        'payload' => <<<'PHP'
        <?php
        
        \Foo::$mainStaticProp;
        ----
        <?php
        
        namespace Humbug;
        
        \Humbug\Foo::$mainStaticProp;
        
        PHP,
    ],
];
