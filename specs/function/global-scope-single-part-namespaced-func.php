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
        'title' => 'Namespaced function call statement in the global scope',
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
    ],

    'Namespaced function call' => <<<'PHP'
    <?php
    
    PHPUnit\main();
    ----
    <?php

    namespace Humbug;

    PHPUnit\main();

    PHP,

    'FQ namespaced function call' => <<<'PHP'
    <?php
    
    \PHPUnit\main();
    ----
    <?php
    
    namespace Humbug;
    
    \Humbug\PHPUnit\main();
    
    PHP,

    'Exposed namespaced function call' => [
        'expose-functions' => ['PHPUnit\main'],
        'expected-recorded-functions' => [
            ['PHPUnit\main', 'Humbug\PHPUnit\main'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        PHPUnit\main();
        ----
        <?php

        namespace Humbug;

        \Humbug\PHPUnit\main();

        PHP,
    ],

    'FQ exposed namespaced function call' => [
        'expose-functions' => ['PHPUnit\main'],
        'expected-recorded-functions' => [
            ['PHPUnit\main', 'Humbug\PHPUnit\main'],
        ],
        'payload' => <<<'PHP'
        <?php
        
        \PHPUnit\main();
        ----
        <?php
        
        namespace Humbug;
        
        \Humbug\PHPUnit\main();
        
        PHP,
    ],
];
