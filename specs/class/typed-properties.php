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
        title: 'Class declaration with typed properties',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

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
        exposeGlobalClasses: true,
        expectedRecordedClasses: [
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
        exposeGlobalClasses: true,
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
        exposeClasses: [
            'Foo\A',
            'Foo\B',
        ],
        expectedRecordedClasses: [
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
