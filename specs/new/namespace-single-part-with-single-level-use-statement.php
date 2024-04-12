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
        title: 'New statement call of a class belonging to the global namespace which has been imported with a use statement in a namespace',
        

        
        
        
        
        
       
       

        
        
        
       

        
       
    ),

    'New statement call of a class' => <<<'PHP'
    <?php
    
    namespace {
        class Foo {}
    }
    
    namespace A {
        use Foo;
        
        new Foo();
    }
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    namespace Humbug\A;
    
    use Humbug\Foo;
    new Foo();
    
    PHP,

    'FQ new statement call of a class' => <<<'PHP'
    <?php
    
    namespace {
        class Foo {}
    }
    
    namespace A {
        use Foo;
        
        new \Foo();
    }
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    namespace Humbug\A;
    
    use Humbug\Foo;
    new \Humbug\Foo();
    
    PHP,
];
