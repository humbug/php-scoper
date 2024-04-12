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
        'title' => 'Use statements for functions',
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

    'Use statement for a function belonging to the global namespace' => <<<'PHP'
    <?php
    
    use function foo as greet;
    
    ----
    <?php
    
    namespace Humbug;
    
    use function Humbug\foo as greet;
    
    PHP,

    'Use statement for a function belonging to the global namespace which has already been prefixed' => <<<'PHP'
    <?php
    
    use function Humbug\foo as greet;
    
    ----
    <?php
    
    namespace Humbug;
    
    use function Humbug\foo as greet;
    
    PHP,

    'Use statement for a namespaced function' => <<<'PHP'
    <?php
    
    use function Foo\bar as greet;
    
    ----
    <?php
    
    namespace Humbug;
    
    use function Humbug\Foo\bar as greet;
    
    PHP,

    'Use statement for a namespaced function which has already been prefixed' => <<<'PHP'
    <?php
    
    use function Humbug\Foo\bar as greet;
    
    ----
    <?php
    
    namespace Humbug;
    
    use function Humbug\Foo\bar as greet;
    
    PHP,

    'Use statement for a namespaced function which has been exposed' => [
        'expose-functions' => ['Foo\bar'],
        'payload' => <<<'PHP'
        <?php
        
        use function Foo\bar as greet;
        
        ----
        <?php
        
        namespace Humbug;
        
        use function Humbug\Foo\bar as greet;
        
        PHP,
    ],
];
