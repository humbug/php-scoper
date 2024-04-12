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
        'title' => 'New statement call of a class imported via an aliased use statement in the global scope',
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

    'New statement call of a class belonging to the global namespace imported via an aliased use statement' => <<<'PHP'
    <?php
    
    namespace {
        class Foo {}
    }
    
    namespace {
        use Foo as X;
        
        new X();
    }
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    namespace Humbug;
    
    use Humbug\Foo as X;
    new X();
    
    PHP,

    'New statement call of a class belonging to the global namespace imported via an aliased use statement; the call is made using the original class instead of the alias' => <<<'PHP'
    <?php
    
    namespace {
        class Foo {}
    }
    
    namespace {
        use Foo as X;
        
        new Foo();
    }
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    namespace Humbug;
    
    use Humbug\Foo as X;
    new Foo();
    
    PHP,

    'FQ new statement call of a class belonging to the global namespace imported via an aliased use statement' => <<<'PHP'
    <?php
    
    namespace {
        class Foo {}
        class X {}
    }
    
    namespace {
        use Foo as X;
        
        new \X();
    }
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    class X
    {
    }
    namespace Humbug;
    
    use Humbug\Foo as X;
    new \Humbug\X();
    
    PHP,

    'FQ new statement call of a class belonging to the global namespace imported via an aliased use statement; the new statement uses the class directly instead of the alias' => <<<'PHP'
    <?php
    
    namespace {
        class Foo {}
        class X {}
    }
    
    namespace {
        use Foo as X;
        
        new \X();
    }
    ----
    <?php
    
    namespace Humbug;
    
    class Foo
    {
    }
    class X
    {
    }
    namespace Humbug;
    
    use Humbug\Foo as X;
    new \Humbug\X();
    
    PHP,

    'New statement call of an internal class imported with an aliased use statement' => <<<'PHP'
    <?php
    
    use ArrayIterator as X;
    
    new X([]);
    ----
    <?php
    
    namespace Humbug;
    
    use ArrayIterator as X;
    new X([]);
    
    PHP,
];
