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
        'title' => 'Single-level namespaced constant call in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

        'expose-global-constants' => false,
        'expose-global-classes' => false,
        'expose-global-functions' => true,
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

    'Namespaced constant call' => <<<'PHP'
    <?php
    
    namespace A;
    
    PHPUnit\DUMMY_CONST;
    ----
    <?php

    namespace Humbug\A;

    PHPUnit\DUMMY_CONST;

    PHP,

    'FQ namespaced constant call' => <<<'PHP'
    <?php
    
    namespace A;
    
    \PHPUnit\DUMMY_CONST;
    ----
    <?php
    
    namespace Humbug\A;
    
    \Humbug\PHPUnit\DUMMY_CONST;
    
    PHP,

    'Exposed namespaced constant call on an exposed constant' => [
        'expose-constants' => ['PHPUnit\DUMMY_CONST'],
        'payload' => <<<'PHP'
        <?php
        
        namespace A;
        
        PHPUnit\DUMMY_CONST;
        ----
        <?php

        namespace Humbug\A;

        PHPUnit\DUMMY_CONST;

        PHP,
    ],

    'Exposed FQ namespaced constant call on an exposed constant' => [
        'expose-constants' => ['PHPUnit\DUMMY_CONST'],
        'payload' => <<<'PHP'
        <?php
        
        namespace A;
        
        \PHPUnit\DUMMY_CONST;
        ----
        <?php
        
        namespace Humbug\A;
        
        \PHPUnit\DUMMY_CONST;
        
        PHP,
    ],
];
