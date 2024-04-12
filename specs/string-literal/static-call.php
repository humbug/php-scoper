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
        'title' => 'Static call with string literal',
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

    'On expression' => <<<'PHP'
    <?php
    
    $fooFactory::create('Acme\Foo');
    
    ----
    <?php
    
    namespace Humbug;
    
    $fooFactory::create('Humbug\\Acme\\Foo');
    
    PHP,

    'On expression with a symbol belonging to the global scope' => <<<'PHP'
    <?php
    
    $fooFactory::create('Foo');
    
    ----
    <?php
    
    namespace Humbug;
    
    $fooFactory::create('Foo');
    
    PHP,

    'On DateTime object' => <<<'PHP'
    <?php
    
    DateTime::create('Acme\Foo');
    
    ----
    <?php
    
    namespace Humbug;
    
    \DateTime::create('Humbug\\Acme\\Foo');
    
    PHP,

    'On DateTime object with a symbol belonging to the global scope' => <<<'PHP'
    <?php
    
    DateTime::create('Foo');
    
    ----
    <?php
    
    namespace Humbug;
    
    \DateTime::create('Foo');
    
    PHP,
];
