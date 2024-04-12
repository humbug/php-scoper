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
        'title' => 'Class static property call of a class imported with an aliased use statement in the global scope',
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

    'Constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
    <?php
    
    class Foo {}
    
    use Foo as X;
    
    X::$mainStaticProp;
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    use Humbug\Foo as X;
    X::$mainStaticProp;
    
    PHP,

    'FQ constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
    <?php
    
    class Foo {}
    class X {}
    
    use Foo as X;
    
    \X::$mainStaticProp;
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
    \Humbug\X::$mainStaticProp;
    
    PHP,

    'Constant call on an internal class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
    <?php
    
    use Reflector as X;
    
    X::$mainStaticProp;
    ----
    <?php
    
    namespace Humbug;
    
    use Reflector as X;
    X::$mainStaticProp;
    
    PHP,

    'FQ constant call on an internal class which is imported via an aliased use statement and which belongs to the global namespace' => <<<'PHP'
    <?php
    
    class X {}
    
    use Reflector as X;
    
    \X::$mainStaticProp;
    ----
    <?php
    
    namespace Humbug;
    
    class X
    {
    }
    use Reflector as X;
    \Humbug\X::$mainStaticProp;
    
    PHP,

    'Constant call on an exposed class which is imported via an aliased use statement and which belongs to the global namespace' => [
        'expose-classes' => ['Foo'],
        'payload' => <<<'PHP'
        <?php
        
        use Foo as X;
        
        X::$mainStaticProp;
        ----
        <?php
        
        namespace Humbug;
        
        use Humbug\Foo as X;
        X::$mainStaticProp;
        
        PHP,
    ],

    'FQ constant call on an exposed class which is imported via an aliased use statement and which belongs to the global namespace' => [
        'expose-classes' => ['Foo'],
        'payload' => <<<'PHP'
        <?php
        
        class X {}
        
        use Foo as X;
        
        \X::$mainStaticProp;
        ----
        <?php
        
        namespace Humbug;
        
        class X
        {
        }
        use Humbug\Foo as X;
        \Humbug\X::$mainStaticProp;
        
        PHP,
    ],
];
