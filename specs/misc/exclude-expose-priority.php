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
        'title' => 'Excluding a symbol/namespace should have precedence over exposing a symbol/namespace',
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

    'namespace' => [
        'exclude-namespaces' => [
            'Acme',
        ],
        'expose-namespaces' => [
            'Acme',
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme {
            class X {}
        }
        
        namespace {
            new \Acme\X();
        }
        
        ----
        <?php

        namespace Acme;
        
        class X
        {
        }
        namespace Humbug;
        
        new \Acme\X();

        PHP,
    ],

    'symbol' => [
        'exclude-classes' => [
            'Acme\X',
        ],
        'expose-classes' => [
            'Acme\X',
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme {
            class X {}
        }
        
        namespace {
            new \Acme\X();
        }
        
        ----
        <?php

        namespace Humbug\Acme;
        
        class X
        {
        }
        namespace Humbug;
        
        new \Acme\X();

        PHP,
    ],
];
