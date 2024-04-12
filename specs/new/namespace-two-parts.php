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
        title: 'New statement call of a namespaced class in a namespace',
        

        exposeGlobalConstants: true,
        
        exposeGlobalFunctions: true,
        

       







        

    ),

    'New statement call of a class' => <<<'PHP'
    <?php
    
    namespace X\Foo {
        class Bar {}
    }
    
    namespace X {
        new Foo\Bar();
    }
    ----
    <?php
    
    namespace Humbug\X\Foo;
    
    class Bar
    {
    }
    namespace Humbug\X;
    
    new Foo\Bar();
    
    PHP,

    'FQ new statement call of a class' => <<<'PHP'
    <?php
    
    namespace Foo {
        class Bar {}
    }
    
    namespace X\Foo {
        class Bar {}
    }
    
    namespace X {
        new \Foo\Bar();
    }
    ----
    <?php
    
    namespace Humbug\Foo;
    
    class Bar
    {
    }
    namespace Humbug\X\Foo;
    
    class Bar
    {
    }
    namespace Humbug\X;
    
    new \Humbug\Foo\Bar();
    
    PHP,
];
