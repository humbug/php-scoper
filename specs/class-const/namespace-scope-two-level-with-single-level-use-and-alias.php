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
        'title' => 'Class constant call of a namespaced class imported with an aliased use statement in a namespace',
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
        'expected-recorded-ambiguous-functions' => [],
    ],

    'Constant call on a namespaced class partially imported with an aliased use statement' => <<<'PHP'
    <?php
    
    namespace {
        class Foo {}
    }
    
    namespace Foo {
        class Bar {}
    }
    
    namespace A {
        use Foo as X;
        
        X\Bar::MAIN_CONST;
    }
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    namespace Humbug\Foo;
    
    class Bar
    {
    }
    namespace Humbug\A;
    
    use Humbug\Foo as X;
    X\Bar::MAIN_CONST;
    
    PHP,

    'Constant call on a namespaced class imported with an aliased use statement' => <<<'PHP'
    <?php
    
    namespace Foo {
        class Bar {}
    }
    
    namespace A {
        use Foo\Bar as X;
        
        X::MAIN_CONST;
    }
    ----
    <?php
    
    namespace Humbug\Foo;
    
    class Bar
    {
    }
    namespace Humbug\A;
    
    use Humbug\Foo\Bar as X;
    X::MAIN_CONST;
    
    PHP,

    'FQ constant call on a namespaced class imported with an aliased use statement' => <<<'PHP'
    <?php
    
    namespace {
        class Foo {}
    }
    
    namespace X {
        class Bar {}
    }
    
    namespace A {
        use Foo as X;
        
        \X\Bar::MAIN_CONST;
    }
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    namespace Humbug\X;
    
    class Bar
    {
    }
    namespace Humbug\A;
    
    use Humbug\Foo as X;
    \Humbug\X\Bar::MAIN_CONST;
    
    PHP,

    'FQ Constant call on an exposed namespaced class partially imported with an aliased use statement' => [
        'expose-classes' => ['Foo\Bar'],
        'expected-recorded-classes' => [
            ['Foo\Bar', 'Humbug\Foo\Bar'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace {
            class Foo {}
        }
        
        namespace Foo {
            class Bar {}
        }
        
        namespace A {
            use Foo as X;
            
            X\Bar::MAIN_CONST;
        }
        ----
        <?php
        
        namespace Humbug;
        
        class Foo
        {
        }
        namespace Humbug\Foo;
        
        class Bar
        {
        }
        \class_alias('Humbug\\Foo\\Bar', 'Foo\\Bar', \false);
        namespace Humbug\A;
        
        use Humbug\Foo as X;
        X\Bar::MAIN_CONST;
        
        PHP,
    ],
];
