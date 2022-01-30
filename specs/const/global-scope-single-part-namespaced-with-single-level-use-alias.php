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
        'title' => 'Single-level namespaced constant call in the global scope which is imported via an aliased use statement',
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

    'Constant call on an imported single-level namespace' => <<<'PHP'
    <?php
    
    namespace {
        class Foo {}
    }
    
    namespace Foo {
        const DUMMY_CONST = '';
    }
    
    namespace {
        use Foo as A;
        
        A\DUMMY_CONST;
    }
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    namespace Humbug\Foo;
    
    const DUMMY_CONST = '';
    namespace Humbug;
    
    use Humbug\Foo as A;
    A\DUMMY_CONST;
    
    PHP,

    'FQ constant call on an imported single-level namespace' => <<<'PHP'
    <?php
    
    namespace {
        class Foo {}
    }
    
    namespace A {
        const DUMMY_CONST = '';
    }
    
    namespace {
        use Foo as A;
        
        \A\DUMMY_CONST;
    }
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    namespace Humbug\A;
    
    const DUMMY_CONST = '';
    namespace Humbug;
    
    use Humbug\Foo as A;
    \Humbug\A\DUMMY_CONST;
    
    PHP,

    'Exposed constant call on an imported single-level namespace' => [
        'expose-constants' => ['Foo\DUMMY_CONST'],
        'payload' => <<<'PHP'
        <?php
        
        namespace {
            class Foo {}
        }
        
        namespace Foo {
            const DUMMY_CONST = '';
        }
        
        namespace {
            use Foo as A;
            
            A\DUMMY_CONST;
        }
        ----
        <?php
        
        namespace Humbug;
        
        class Foo
        {
        }
        namespace Humbug\Foo;
        
        \define('Foo\\DUMMY_CONST', '');
        namespace Humbug;
        
        use Humbug\Foo as A;
        \Foo\DUMMY_CONST;
        
        PHP,
    ],
];
