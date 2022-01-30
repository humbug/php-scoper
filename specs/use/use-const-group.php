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
        'title' => 'Use statements for constants with group statements',
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
    
    use const A\{B};
    use const A\{B\C, D};
    use const \A\B\{C\D as ABCD, E};
    
    B;
    C;
    D;
    ABCD;
    E;
    
    ----
    <?php
    
    namespace Humbug;
    
    use const Humbug\A\B;
    use const Humbug\A\B\C;
    use const Humbug\A\D;
    use const Humbug\A\B\C\D as ABCD;
    use const Humbug\A\B\E;
    B;
    C;
    D;
    ABCD;
    E;
    
    PHP,
];
