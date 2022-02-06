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
        'title' => 'global function call in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

        'expose-global-constants' => true,
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

    // We don't do anything as there is no ways to distinguish between a namespaced function call
    // from the same namespace and a function registered in the global scope
    'single-part' => <<<'PHP'
    <?php
    
    namespace X;
    
    main();
    ----
    <?php
    
    namespace Humbug\X;
    
    main();
    
    PHP,

    'FQ single-part' => <<<'PHP'
    <?php
    
    namespace X;
    
    \main();
    ----
    <?php
    
    namespace Humbug\X;
    
    \Humbug\main();
    
    PHP,

    // In theory this case CAN be wrong. There is however a very high chance it
    // is not as it implies having both A\foo() and foo() in the
    // codebase with only foo() exposed.
    'Exposed constant call in a namespace' => [
        'expose-functions' => ['foo'],
        'payload' => <<<'PHP'
        <?php
        
        namespace A;
        
        foo();
        ----
        <?php
        
        namespace Humbug\A;
        
        \Humbug\foo();
        
        PHP,
    ],
];
