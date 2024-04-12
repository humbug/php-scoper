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
        title: 'Conditional class declaration',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'Declaration in the global namespace' => <<<'PHP'
    <?php
    
    if (true) {
        class A {}
    }
    ----
    <?php
    
    namespace Humbug;
    
    if (\true) {
        class A
        {
        }
    }
    
    PHP,

    'Declaration of an exposed class in the global namespace' => [
        exposeClasses: ['A'],
        expectedRecordedClasses: [
            ['A', 'Humbug\A'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        if (true) {
            class A {}
        }
        ----
        <?php
        
        namespace Humbug;
        
        if (\true) {
            class A
            {
            }
            \class_alias('Humbug\\A', 'A', \false);
        }
        
        PHP,
    ],

    'Declaration in a namespace' => <<<'PHP'
    <?php
    
    namespace Foo;
    
    if (true) {
        class A {}
    }
    ----
    <?php
    
    namespace Humbug\Foo;
    
    if (\true) {
        class A
        {
        }
    }
    
    PHP,

    'Declaration of an exposed class' => [
        exposeClasses: ['Foo\A'],
        expectedRecordedClasses: [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Foo;
        
        if (true) {
            class A {}
        }
        ----
        <?php
        
        namespace Humbug\Foo;
        
        if (\true) {
            class A
            {
            }
            \class_alias('Humbug\\Foo\\A', 'Foo\\A', \false);
        }
        
        PHP,
    ],

    'Multiple declarations in different namespaces' => <<<'PHP'
    <?php
    
    namespace X {
        if (true) {
            class A {}
        }
    }
    
    namespace Y {
        if (true) {
            class B {}
        }
    }
    
    namespace Z {
        if (true) {
            class C {}
        }
    }
    ----
    <?php
    
    namespace Humbug\X;
    
    if (\true) {
        class A
        {
        }
    }
    namespace Humbug\Y;
    
    if (\true) {
        class B
        {
        }
    }
    namespace Humbug\Z;
    
    if (\true) {
        class C
        {
        }
    }
    
    PHP,
];
