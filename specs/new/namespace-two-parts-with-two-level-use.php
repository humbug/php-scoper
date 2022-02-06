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
        'title' => 'New statement call of a namespaced class imported with a use statement in a namespace',
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

    'New statement call of a class via a use statement' => <<<'PHP'
    <?php
    
    namespace X {
        class Foo {}
    }
    
    namespace X\Foo {
        class Bar {}
    }
    
    namespace A {
        use X\Foo;
        
        new Foo\Bar();
    }
    ----
    <?php
    
    namespace Humbug\X;
    
    class Foo
    {
    }
    namespace Humbug\X\Foo;
    
    class Bar
    {
    }
    namespace Humbug\A;
    
    use Humbug\X\Foo;
    new Foo\Bar();
    
    PHP,

    'FQ new statement call of a class via a use statement' => <<<'PHP'
    <?php
    
    namespace X {
        class Foo {}
    }
    
    namespace Foo {
        class Bar {}
    }
    
    namespace A {
        use X\Foo;
        
        new \Foo\Bar();
    }
    ----
    <?php
    
    namespace Humbug\X;
    
    class Foo
    {
    }
    namespace Humbug\Foo;
    
    class Bar
    {
    }
    namespace Humbug\A;
    
    use Humbug\X\Foo;
    new \Humbug\Foo\Bar();
    
    PHP,

    'New statement call of an exposed class via a use statement' => [
        'expose-classes' => ['X\Foo\Bar'],
        'expected-recorded-classes' => [
            ['X\Foo\Bar', 'Humbug\X\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace X {
            class Foo {}
        }
        
        namespace X\Foo {
            class Bar {}
        }
        
        namespace A {
            use X\Foo;
            
            new Foo\Bar();
        }
        ----
        <?php
        
        namespace Humbug\X;
        
        class Foo
        {
        }
        namespace Humbug\X\Foo;
        
        class Bar
        {
        }
        \class_alias('Humbug\\X\\Foo\\Bar', 'X\\Foo\\Bar', \false);
        namespace Humbug\A;
        
        use Humbug\X\Foo;
        new Foo\Bar();
        
        PHP,
    ],

    'FQ new statement call of a non-exposed class via a use statement' => [
        'expose-classes' => ['X\Foo\Bar'],
        'payload' => <<<'PHP'
        <?php
        
        namespace X {
            class Foo {}
        }
        
        namespace Foo {
            class Bar {}
        }
        
        namespace A {
            use X\Foo;
            
            new \Foo\Bar();
        }
        ----
        <?php
        
        namespace Humbug\X;
        
        class Foo
        {
        }
        namespace Humbug\Foo;
        
        class Bar
        {
        }
        namespace Humbug\A;
        
        use Humbug\X\Foo;
        new \Humbug\Foo\Bar();
        
        PHP,
    ],
];
