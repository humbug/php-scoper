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
        'title' => 'Class declaration with typed properties',
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
    
    class A {
        public string $name;
        
        public ?self $instance = null;
        
        public static ?self $staticInstance = null;
        
        public ?B $foo;
    
        public function a() {}
    }
    ----
    <?php
    
    namespace Humbug;
    
    class A
    {
        public string $name;
        public ?self $instance = null;
        public static ?self $staticInstance = null;
        public ?B $foo;
        public function a()
        {
        }
    }
    
    PHP,

    'Declaration in the global namespace with global classes exposed' => [
        'expose-global-classes' => true,
        'expected-recorded-classes' => [
            ['A', 'Humbug\A'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        class A {
            public string $name;
            
            public ?B $foo;
        
            public function a() {}
        }
        ----
        <?php
        
        namespace Humbug;
        
        class A
        {
            public string $name;
            public ?B $foo;
            public function a()
            {
            }
        }
        \class_alias('Humbug\\A', 'A', \false);
        
        PHP,
    ],

    'Declaration in a namespace' => <<<'PHP'
    <?php
    
    namespace Foo;
    
    class A
    {
        public string $name;
        public ?self $instance = null;
        public static ?self $staticInstance = null;
        public ?B $foo;
        public function a()
        {
        }
    }
    ----
    <?php
    
    namespace Humbug\Foo;
    
    class A
    {
        public string $name;
        public ?self $instance = null;
        public static ?self $staticInstance = null;
        public ?B $foo;
        public function a()
        {
        }
    }
    
    PHP,

    'Declaration in a namespace with global classes exposed' => [
        'expose-global-classes' => true,
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo;
        
        class A {
            public string $name;
            public ?B $foo;
            public function a() {}
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        class A
        {
            public string $name;
            public ?B $foo;
            public function a()
            {
            }
        }
        
        PHP,
    ],

    'Declaration of a namespaced exposed class' => [
        'expose-classes' => [
            'Foo\A',
            'Foo\B',
        ],
        'expected-recorded-classes' => [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo;
        
        class A {
            public string $name;
            public ?B $foo;
            public function a() {}
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        class A
        {
            public string $name;
            public ?B $foo;
            public function a()
            {
            }
        }
        \class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);
        
        PHP,
    ],

    'Declaration in a namespace with use statements' => <<<'PHP'
    <?php
    
    namespace Foo;
    
    use Bar\C;
    use DateTimeImmutable;
    
    class A
    {
        public string $name;
        public ?B $foo;
        public ?C $foo;
        public ?DateTimeImmutable $bar;
        public ?Closure $baz;
        public function a()
        {
        }
    }
    ----
    <?php
    
    namespace Humbug\Foo;
    
    use Humbug\Bar\C;
    use DateTimeImmutable;
    class A
    {
        public string $name;
        public ?B $foo;
        public ?C $foo;
        public ?DateTimeImmutable $bar;
        public ?Closure $baz;
        public function a()
        {
        }
    }
    
    PHP,
];
