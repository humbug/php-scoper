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
        'title' => 'Disjoint Normal Form (DNF) types',
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

    'Property' => <<<'PHP'
    <?php
    
    class X
    {
        public (A&B)|(X&Y) $prop1;
        public (\U\A&\U\B)|(\U\X&\U\Y) $prop2;
    }
    
    ----
    <?php
    
    namespace Humbug;

    class X
    {
        public (A&B)|(X&Y) $prop1;
        public (\Humbug\U\A&\Humbug\U\B)|(\Humbug\U\X&\Humbug\U\Y) $prop2;
    }
    
    PHP,

    'Function' => <<<'PHP'
    <?php
    
    function test((A&B)|(X&Y) $a): (A&B)|(X&Y) {}
    function test((\U\A&\U\B)|(\U\X&\U\Y) $a): (\U\A&\U\B)|(\U\X&\U\Y) {}
    
    ----
    <?php
    
    namespace Humbug;
    
    function test((A&B)|(X&Y) $a) : (A&B)|(X&Y)
    {
    }
    function test((\Humbug\U\A&\Humbug\U\B)|(\Humbug\U\X&\Humbug\U\Y) $a) : (\Humbug\U\A&\Humbug\U\B)|(\Humbug\U\X&\Humbug\U\Y)
    {
    }
    
    PHP,
];
