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
        'title' => 'Internal symbols defined by the user',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => false,
        'expose-global-classes' => false,
        'expose-global-functions' => false,
        'excluded-constants' => [],
        'excluded-classes' => [],
        'excluded-functions' => [],
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Known non-internal symbols (sanity test)' => <<<'PHP'
    <?php
    
    use Foo;
    use const BAR;
    use function baz;
    
    ----
    <?php
    
    namespace Humbug;
    
    use Humbug\Foo;
    use const Humbug\BAR;
    use function Humbug\baz;
    
    PHP,

    'Known non-internal symbols with global whitelisting (sanity check)' => [
        'whitelist-global-constants' => true,
        'expose-global-classes' => true,
        'expose-global-functions' => true,
        'payload' => <<<'PHP'
            <?php
            
            use Foo;
            use const BAR;
            use function baz;
            
            ----
            <?php
            
            namespace Humbug;
            
            use Humbug\Foo;
            use const BAR;
            use function Humbug\baz;
            
            PHP,
    ],

    'Declared internal symbols' => [
        'excluded-classes' => ['Foo'],
        'excluded-functions' => ['baz'],
        'excluded-constants' => ['BAR'],
        'payload' => <<<'PHP'
            <?php
            
            use Foo;
            use const BAR;
            use function baz;
            
            ----
            <?php
            
            namespace Humbug;
            
            use Foo;
            use const BAR;
            use function baz;
            
            PHP,
    ],
];
