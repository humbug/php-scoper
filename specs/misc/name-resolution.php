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
        'title' => 'Name resolution',
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

    'Internal class & function with the same name' => [
        'expected-recorded-functions' => [],
        'payload' => <<<'PHP'
        <?php
        
        namespace PHPUnit\Framework;
        
        use function assert;
        
        abstract class TestCase extends Assert {
            function __construct() {
                \assert();
            }
        }
        
        ----
        <?php
        
        namespace Humbug\PHPUnit\Framework;
        
        use function assert;
        abstract class TestCase extends Assert
        {
            function __construct()
            {
                \assert();
            }
        }
        
        PHP,
    ],

    'Internal class & const with the same name' => [
        'expected-recorded-functions' => [],
        'payload' => <<<'PHP'
        <?php
        
        namespace PHPUnit\Framework;
        
        use const SORT_NUMERIC;
        
        abstract class TestCase extends SORT_NUMERIC {
            function __construct() {
                echo SORT_NUMERIC;
            }
        }
        
        ----
        <?php
        
        namespace Humbug\PHPUnit\Framework;
        
        use const SORT_NUMERIC;
        abstract class TestCase extends SORT_NUMERIC
        {
            function __construct()
            {
                echo SORT_NUMERIC;
            }
        }
        
        PHP,
    ],
];
