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
        title: 'Excluding a symbol/namespace should have precedence over exposing a symbol/namespace',
        

        
        
        












    ),

    'namespace' => [
        excludeNamespaces: [
            'Acme',
        ],
        exposeNamespaces: [
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
        excludeClasses: [
            'Acme\X',
        ],
        exposeClasses: [
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
