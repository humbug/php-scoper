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
        'title' => 'New statement call of a namespaced class in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

        'expose-global-constants' => true,
        'expose-global-classes' => false,
        'expose-global-functions' => true,
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
        'expected-recorded-ambiguous-functions' => [],
    ],

    'New statement call of a namespaced class' => <<<'PHP'
    <?php
    
    namespace Foo {
        class Bar {}
    }
    
    namespace {
        new Foo\Bar();
    }
    ----
    <?php
    
    namespace Humbug\Foo;
    
    class Bar
    {
    }
    namespace Humbug;
    
    new Foo\Bar();
    
    PHP,

    'FQ new statement call of a namespaced class' => <<<'PHP'
    <?php
    
    namespace Foo {
        class Bar {}
    }
    
    namespace {
        new \Foo\Bar();
    }
    ----
    <?php
    
    namespace Humbug\Foo;
    
    class Bar
    {
    }
    namespace Humbug;
    
    new \Humbug\Foo\Bar();
    
    PHP,

    'New statement call of an exposed namespaced class' => [
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
            new Foo\Bar();
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        class Bar
        {
        }
        \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
        namespace Humbug;
        
        new \Humbug\Foo\Bar();
        
        PHP,
    ],

    'FQ new statement call of an exposed namespaced class' => [
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
            new \Foo\Bar();
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        class Bar
        {
        }
        \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
        namespace Humbug;
        
        new \Humbug\Foo\Bar();
        
        PHP,
    ],
];
