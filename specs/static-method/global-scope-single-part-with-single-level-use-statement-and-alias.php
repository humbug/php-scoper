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
        'title' => 'Static method call statement of a class imported via an aliased use statement in the global scope',
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

    'Static method call statement of a class belonging to the global namespace imported via an aliased use statement' => <<<'PHP'
    <?php
    
    class Foo {}
    
    use Foo as X;
    
    X::main();
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    use Humbug\Foo as X;
    X::main();
    
    PHP,

    'FQ static method call statement of a class belonging to the global namespace imported via an aliased use statement' => <<<'PHP'
    <?php
    
    class Foo {}
    class X {}
    
    use Foo as X;
    
    \X::main();
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    class X
    {
    }
    use Humbug\Foo as X;
    \Humbug\X::main();
    
    PHP,

    'Static method call statement of a class belonging to the global namespace which has been exposed' => [
        'expose-global-classes' => true,
        'payload' => <<<'PHP'
        <?php
        
        use Closure as X;
        
        X::bind();
        ----
        <?php
        
        namespace Humbug;
        
        use Closure as X;
        X::bind();
        
        PHP,
    ],

    'FQ static method call statement of a class belonging to the global namespace which has been exposed' => [
        'expose-global-classes' => true,
        'expected-recorded-classes' => [
            ['X', 'Humbug\X'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        class X {}
        
        use Closure as X;
        
        \X::bind();
        ----
        <?php
        
        namespace Humbug;
        
        class X
        {
        }
        \class_alias('Humbug\\X', 'X', \false);
        use Closure as X;
        \Humbug\X::bind();
        
        PHP,
    ],
];
