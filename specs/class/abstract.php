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
        title: 'Abstract class declaration',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'Declaration in the global namespace' => <<<'PHP'
    <?php
    
    abstract class A {
        public function a() {}
        abstract public function b();
    }
    ----
    <?php
    
    namespace Humbug;
    
    abstract class A
    {
        public function a()
        {
        }
        public abstract function b();
    }
    
    PHP,

    'Declaration in the global namespace with global classes exposed' => [
        exposeGlobalClasses: true,
        expectedRecordedClasses: [
            ['A', 'Humbug\A'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        abstract class A {
            public function a() {}
            abstract public function b();
        }
        ----
        <?php
        
        namespace Humbug;
        
        abstract class A
        {
            public function a()
            {
            }
            public abstract function b();
        }
        \class_alias('Humbug\\A', 'A', \false);
        
        PHP,
    ],

    'Declaration in the global namespace with the global namespace excluded' => [
        excludeNamespaces: ['/^$/'],
        expectedRecordedClasses: [
            ['A', 'Humbug\A'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        abstract class A {
            public function a() {}
            abstract public function b();
        }
        ----
        <?php
        
        namespace {
            abstract class A
            {
                public function a()
                {
                }
                public abstract function b();
            }
            \class_alias('Humbug\\A', 'A', \false);
        }
        
        PHP,
    ],

    'Declaration of an exposed class in the global namespace' => [
        exposeClasses: ['A'],
        expectedRecordedClasses: [
            ['A', 'Humbug\A'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        abstract class A {
            public function a() {}
            abstract public function b();
        }
        ----
        <?php
        
        namespace Humbug;
        
        abstract class A
        {
            public function a()
            {
            }
            public abstract function b();
        }
        \class_alias('Humbug\\A', 'A', \false);
        
        PHP,
    ],

    'Declaration of an exposed class in the global namespace which is excluded' => [
        excludeNamespaces: ['/^$/'],
        exposeClasses: ['A'],
        expectedRecordedClasses: [
            ['A', 'Humbug\A'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        abstract class A {
            public function a() {}
            abstract public function b();
        }
        ----
        <?php
        
        namespace {
            abstract class A
            {
                public function a()
                {
                }
                public abstract function b();
            }
            \class_alias('Humbug\\A', 'A', \false);
        }
        
        PHP,
    ],

    'Declaration in a namespace' => <<<'PHP'
    <?php
    
    namespace Foo;
    
    abstract class A {
        public function a() {}
        abstract public function b();
    }
    ----
    <?php
    
    namespace Humbug\Foo;
    
    abstract class A
    {
        public function a()
        {
        }
        public abstract function b();
    }
    
    PHP,

    'Declaration in a namespace with global classes exposed' => [
        exposeGlobalClasses: true,
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo;
        
        abstract class A {
            public function a() {}
            abstract public function b();
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        abstract class A
        {
            public function a()
            {
            }
            public abstract function b();
        }
        
        PHP,
    ],

    'Declaration in an excluded namespace' => [
        excludeNamespaces: ['Foo'],
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo;
        
        abstract class A {
            public function a() {}
            abstract public function b();
        }
        ----
        <?php
        
        namespace Foo;
        
        abstract class A
        {
            public function a()
            {
            }
            public abstract function b();
        }
        
        PHP,
    ],

    'Declaration in an exposed namespace' => [
        exposeNamespaces: ['Foo'],
        expectedRecordedClasses: [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo;
        
        abstract class A {
            public function a() {}
            abstract public function b();
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        abstract class A
        {
            public function a()
            {
            }
            public abstract function b();
        }
        \class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);
        
        PHP,
    ],

    'Declaration of an exposed class in a namespace' => [
        exposeClasses: ['Foo\A'],
        expectedRecordedClasses: [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo;
        
        abstract class A {
            public function a() {}
            abstract public function b();
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        abstract class A
        {
            public function a()
            {
            }
            public abstract function b();
        }
        \class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);
        
        PHP,
    ],

    'Declaration of an exposed namespaced class belonging to an excluded namespace' => [
        excludeNamespaces: ['Foo'],
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo;
        
        abstract class A {
            public function a() {}
        }
        
        abstract class AA {}
        
        abstract class B {}
        
        namespace Foo\A;
        
        abstract class B {}
        
        ----
        <?php
        
        namespace Foo;
        
        abstract class A
        {
            public function a()
            {
            }
        }
        abstract class AA
        {
        }
        abstract class B
        {
        }
        namespace Foo\A;
        
        abstract class B
        {
        }
        
        PHP,
    ],

    'Declaration of a class belonging to an excluded namespace' => [
        excludeNamespaces: ['Foo'],
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo;
        
        abstract class A {
            public function a() {}
            abstract public function b();
        }
        ----
        <?php
        
        namespace Foo;
        
        abstract class A
        {
            public function a()
            {
            }
            public abstract function b();
        }
        
        PHP,
    ],

    'Multiple declarations in different namespaces with exposed classes' => [
        exposeClasses: ['Foo\WA', 'Bar\WB', 'WC'],
        expectedRecordedClasses: [
            ['Foo\WA', 'Humbug\Foo\WA'],
            ['Bar\WB', 'Humbug\Bar\WB'],
            ['WC', 'Humbug\WC'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo {
        
            abstract class A {
                public function a() {}
            }
        
            abstract class WA {
                public function a() {}
            }
        }
        
        namespace Bar {
        
            abstract class B {
                public function b() {}
            }
        
            abstract class WB {
                public function b() {}
            }
        }
        
        namespace {
        
            abstract class C {
                public function c() {}
            }
        
            abstract class WC {
                public function c() {}
            }
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        abstract class A
        {
            public function a()
            {
            }
        }
        abstract class WA
        {
            public function a()
            {
            }
        }
        \class_alias('Humbug\\Foo\\WA', 'Foo\\WA', \false);
        namespace Humbug\Bar;
        
        abstract class B
        {
            public function b()
            {
            }
        }
        abstract class WB
        {
            public function b()
            {
            }
        }
        \class_alias('Humbug\\Bar\\WB', 'Bar\\WB', \false);
        namespace Humbug;
        
        abstract class C
        {
            public function c()
            {
            }
        }
        abstract class WC
        {
            public function c()
            {
            }
        }
        \class_alias('Humbug\\WC', 'WC', \false);
        
        PHP,
    ],
];
