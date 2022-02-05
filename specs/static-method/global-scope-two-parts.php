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
        'title' => 'Static method call statement of a namespaced class in the global scope',
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

    'Static method call statement of a namespaced class' => <<<'PHP'
    <?php
    
    namespace Foo {
        class Bar {}
    }
    
    namespace {
        Foo\Bar::main();
    }
    ----
    <?php
    
    namespace Humbug\Foo;
    
    class Bar
    {
    }
    namespace Humbug;
    
    Foo\Bar::main();
    
    PHP,

    'FQ static method call statement of a namespaced class' => <<<'PHP'
    namespace Foo {
        class Bar {}
    }
    
    namespace {
        \Foo\Bar::main();
    }
    ----
    namespace Foo {
        class Bar {}
    }
    
    namespace {
        \Foo\Bar::main();
    }
    
    PHP,

    'Static method call statement of a namespaced class which has been exposed' => [
        'expose-classes' => ['Foo\Bar'],
        'expected-recorded-classes' => [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo {
            class Bar {}
        }
        
        namespace {
            Foo\Bar::main();
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        class Bar
        {
        }
        \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
        namespace Humbug;
        
        \Humbug\Foo\Bar::main();
        
        PHP,
    ],

    'FQ static method call statement of a namespaced class which has been exposed' => [
        'expose-classes' => ['Foo\Bar'],
        'expected-recorded-classes' => [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo {
            class Bar {}
        }
        
        namespace {
            \Foo\Bar::main();
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        class Bar
        {
        }
        \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
        namespace Humbug;
        
        \Humbug\Foo\Bar::main();
        
        PHP,
    ],
];
