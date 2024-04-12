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
        'title' => 'Namespaces with an outside statement',
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

    'Declare statement' => <<<'PHP'
    <?php
    
    declare(A='B');
    
    namespace Foo;
    
    ----
    <?php
    
    declare (A='B');
    namespace Humbug\Foo;
    
    
    PHP,

    'Comment' => <<<'PHP'
    <?php
    
    /* Comment */
    
    namespace Foo;
    
    ----
    <?php
    
    /* Comment */
    namespace Humbug\Foo;
    
    
    PHP,

    'Simple comment' => <<<'PHP'
    <?php
    
    // Comment
    
    namespace Foo;
    
    ----
    <?php
    
    // Comment
    namespace Humbug\Foo;
    
    
    PHP,

    'Doc block' => <<<'PHP'
    <?php
    
    /** Comment */
    
    namespace Foo;
    
    ----
    <?php
    
    /** Comment */
    namespace Humbug\Foo;
    
    
    PHP,
];
