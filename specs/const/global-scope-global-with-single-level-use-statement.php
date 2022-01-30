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
        'title' => 'Global constant imported with a use statement used in the global scope',
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

    'Constant call imported with a use statement' => <<<'PHP'
    <?php
    
    use const DUMMY_CONST;
    
    DUMMY_CONST;
    ----
    <?php
    
    namespace Humbug;
    
    use const Humbug\DUMMY_CONST;
    DUMMY_CONST;
    
    PHP,

    'Exposed constant call imported with a use statement' => [
        'expose-constants' => ['DUMMY_CONST'],
        'payload' => <<<'PHP'
        <?php
        
        use const DUMMY_CONST;
        
        DUMMY_CONST;
        ----
        <?php
        
        namespace Humbug;
        
        use const DUMMY_CONST;
        DUMMY_CONST;
        
        PHP,
    ],

    'FQ constant call imported with a use statement' => <<<'PHP'
    <?php
    
    use const DUMMY_CONST;
    
    \DUMMY_CONST;
    ----
    <?php
    
    namespace Humbug;
    
    use const Humbug\DUMMY_CONST;
    \Humbug\DUMMY_CONST;
    
    PHP,
];
