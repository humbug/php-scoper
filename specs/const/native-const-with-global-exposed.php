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
        'title' => 'Native constant calls with the global constants exposed',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

        'expose-global-constants' => true,
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

    'Internal function in a namespace' => <<<'PHP'
    <?php
    
    namespace Acme;
    
    $x = DIRECTORY_SEPARATOR;
    
    ----
    <?php
    
    namespace Humbug\Acme;
    
    $x = \DIRECTORY_SEPARATOR;
    
    PHP,

    'Namespaced function having the same name as an internal function' => <<<'PHP'
    <?php
    
    namespace Acme;
    
    use const Acme\DIRECTORY_SEPARATOR;
    
    $x = DIRECTORY_SEPARATOR;
    
    ----
    <?php
    
    namespace Humbug\Acme;
    
    use const Humbug\Acme\DIRECTORY_SEPARATOR;
    $x = DIRECTORY_SEPARATOR;
    
    PHP,
];
