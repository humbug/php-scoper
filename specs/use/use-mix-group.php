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
        'title' => 'Mixed use statements with group statements',
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

    <<<'PHP'
    <?php
    
    use A\B\{C\D, function b\c, const D};
    
    D::class;
    c();
    D;
    
    ----
    <?php
    
    namespace Humbug;
    
    use Humbug\A\B\C\D;
    use function Humbug\A\B\b\c;
    use const Humbug\A\B\D;
    D::class;
    c();
    D;
    
    PHP,
];
