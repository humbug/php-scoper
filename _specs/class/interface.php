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
        'title' => 'Interface declaration',
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

    'Declaration in the global namespace' => <<<'PHP'
    <?php
    
    class C {}
    class D {}
    
    interface A extends C, D, Iterator {
        public function a();
    }
    ----
    <?php
    
    namespace Humbug;
    
    class C
    {
    }
    class D
    {
    }
    interface A extends C, D, \Iterator
    {
        public function a();
    }
    
    PHP,

    'Declaration in the global namespace with global classes exposed' => [
        'expose-global-classes' => true,
        'expected-recorded-classes' => [
            ['A', 'Humbug\A'],
            ['C', 'Humbug\C'],
            ['D', 'Humbug\D'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        class C {}
        class D {}
        
        interface A extends C, D, Iterator {
            public function a();
        }
        ----
        <?php
        
        namespace Humbug;
        
        class C
        {
        }
        \class_alias('Humbug\\C', 'C', \false);
        class D
        {
        }
        \class_alias('Humbug\\D', 'D', \false);
        interface A extends C, D, \Iterator
        {
            public function a();
        }
        \class_alias('Humbug\\A', 'A', \false);
        
        PHP,
    ],

    'Declaration in a namespace' => <<<'PHP'
    <?php
    
    namespace Foo;
    
    use Iterator;
    
    class C {}
    class D {}
    
    interface A extends C, D, Iterator
    {
        public function a();
    }
    ----
    <?php
    
    namespace Humbug\Foo;
    
    use Iterator;
    class C
    {
    }
    class D
    {
    }
    interface A extends C, D, Iterator
    {
        public function a();
    }
    
    PHP,

    'Declaration in a namespace with global classes exposed' => [
        'expose-global-classes' => true,
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo;
        
        use Iterator;
        
        class C {}
        class D {}
        
        interface A extends C, D, Iterator
        {
            public function a();
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        use Iterator;
        class C
        {
        }
        class D
        {
        }
        interface A extends C, D, Iterator
        {
            public function a();
        }
        
        PHP,
    ],

    'Declaration of an exposed interface' => [
        'expose-classes' => ['Foo\A'],
        'expected-recorded-classes' => [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo;
        
        use Iterator;
        
        class C {}
        class D {}
        
        interface A extends C, D, Iterator
        {
            public function a();
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        use Iterator;
        class C
        {
        }
        class D
        {
        }
        interface A extends C, D, Iterator
        {
            public function a();
        }
        \class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);
        
        PHP,
    ],

    'Multiple declarations in different namespaces' => <<<'PHP'
    <?php
    
    namespace X {
        class D {}
        class E {}
    
        interface A extends D, E
        {
            public function a();
        }
    }
    
    namespace Y {
        class D {}
        class E {}
    
        interface B extends D, E
        {
            public function a();
        }
    }
    
    namespace Z {
        class D {}
        class E {}
    
        interface C extends D, E
        {
            public function a();
        }
    }
    ----
    <?php
    
    namespace Humbug\X;
    
    class D
    {
    }
    class E
    {
    }
    interface A extends D, E
    {
        public function a();
    }
    namespace Humbug\Y;
    
    class D
    {
    }
    class E
    {
    }
    interface B extends D, E
    {
        public function a();
    }
    namespace Humbug\Z;
    
    class D
    {
    }
    class E
    {
    }
    interface C extends D, E
    {
        public function a();
    }
    
    PHP,
];
