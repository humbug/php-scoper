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
        'title' => 'String literal assigned to a variable',
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

    'PHP heredoc as argument' => <<<'PHP'
    <?php
    
    declare(strict_types=1);
    
    namespace Acme;
    
    sprintf( <<<'_PHP'
    if (!function_exists('%1$s')) {
        function %1$s() {
            return \%2$s(func_get_args());
        }
    }
    _PHP
            ,
            'foo',
            'bar'
    );
    
    ----
    <?php
    
    declare (strict_types=1);
    namespace Humbug\Acme;
    
    \sprintf(<<<'_PHP'
    if (!function_exists('%1$s')) {
        function %1$s() {
            return \%2$s(func_get_args());
        }
    }
    _PHP
    , 'foo', 'bar');
    
    PHP,
];
