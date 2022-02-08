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
        'title' => 'Union types',
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

    'Method casts' => <<<'PHP'
    <?php
    
    class X
    {
        public function method1(Y&Z $a, Y $b) : Y&Z
        {
        }
    }
    
    ----
    <?php
    
    namespace Humbug;
    
    class X
    {
        public function method1(Y&Z $a, Y $b) : Y&Z
        {
        }
    }
    
    PHP,

    'Function casts' => <<<'PHP'
    <?php
    
    function fun1(Y&Z $a) : Y&Z
    {
    }
    
    ----
    <?php
    
    namespace Humbug;
    
    function fun1(Y&Z $a) : Y&Z
    {
    }

    PHP,

    'Property casts' => <<<'PHP'
    <?php
    
    class X
    {
        private Y&Z $x;
    }
    
    ----
    <?php
    
    namespace Humbug;
    
    class X
    {
        private Y&Z $x;
    }
    
    PHP,

    'Trait casts' => <<<'PHP'
    <?php
    
    trait X
    {
        private Y&Z $x;
        public function method1(Y&Z $a) : Y&Z
        {
        }
    }
    
    ----
    <?php
    
    namespace Humbug;
    
    trait X
    {
        private Y&Z $x;
        public function method1(Y&Z $a) : Y&Z
        {
        }
    }
    
    PHP,

    'Interface casts' => <<<'PHP'
    <?php
    
    interface X
    {
        public function method1(Y&Z $a) : Y&Z;
    }
    
    ----
    <?php
    
    namespace Humbug;
    
    interface X
    {
        public function method1(Y&Z $a) : Y&Z;
    }
    
    PHP,
];
