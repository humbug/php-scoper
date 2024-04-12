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
        'title' => 'Exposed functions which are never declared',
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

    'Non exposed global function call' => <<<'PHP'
    <?php
    
    main();
    ----
    <?php
    
    namespace Humbug;
    
    main();
    
    PHP,

    'Exposed global function call' => [
        'expose-functions' => ['main'],
        'expected-recorded-functions' => [
            ['main', 'Humbug\main'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        main();
        ----
        <?php
        
        namespace Humbug;
        
        \Humbug\main();
        
        PHP,
    ],

    'Global function call with exposed global functions' => [
        'expose-global-functions' => true,
        'expected-recorded-functions' => [
            ['main', 'Humbug\main'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        main();
        ----
        <?php
        
        namespace Humbug;
        
        main();
        
        PHP,
    ],

    'Global function call with non-exposed global functions' => <<<'PHP'
    <?php
    
    main();
    ----
    <?php
    
    namespace Humbug;
    
    main();
    
    PHP,

    'Exposed namespaced function call' => [
        'expose-functions' => ['Acme\main'],
        'expected-recorded-functions' => [],   // Nothing registered here since the FQ could not be resolved
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme;
        
        main();
        ----
        <?php
        
        namespace Humbug\Acme;
        
        main();
        
        PHP,
    ],
];
