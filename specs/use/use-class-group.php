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
        'title' => 'Use statements for classes with group use statements',
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

    'Multiple group use statement' => <<<'PHP'
    <?php
    
    use A\{B};
    use A\{B\C, D};
    use \A\B\{C\D as ABCD, E};
    
    B::class;
    C::class;
    D::class;
    ABCD::class;
    E::class;
    
    ----
    <?php
    
    namespace Humbug;
    
    use Humbug\A\B;
    use Humbug\A\B\C;
    use Humbug\A\D;
    use Humbug\A\B\C\D as ABCD;
    use Humbug\A\B\E;
    B::class;
    C::class;
    D::class;
    ABCD::class;
    E::class;
    
    PHP,

    'Multiple group use statement which are already prefixed' => <<<'PHP'
    <?php
    
    use Humbug\A\{B};
    use Humbug\A\{B\C, D};
    use \Humbug\A\B\{C\E, F};
    
    ----
    <?php
    
    namespace Humbug;
    
    use Humbug\A\B;
    use Humbug\A\B\C;
    use Humbug\A\D;
    use Humbug\A\B\C\E;
    use Humbug\A\B\F;
    
    PHP,

    'Multiple group use statement with exposed classes' => [
        'expose-classes' => [
            'A\B',
            'A\B\C',
        ],
        'payload' => <<<'PHP'
        <?php
        
        use A\{B};
        use A\{B\C, D};
        use \A\B\{C\G, E};
        
        B::class;
        C::class;
        D::class;
        G::class;
        E::class;
        
        ----
        <?php
        
        namespace Humbug;
        
        use Humbug\A\B;
        use Humbug\A\B\C;
        use Humbug\A\D;
        use Humbug\A\B\C\G;
        use Humbug\A\B\E;
        B::class;
        C::class;
        D::class;
        G::class;
        E::class;
        
        PHP,
    ],
];
