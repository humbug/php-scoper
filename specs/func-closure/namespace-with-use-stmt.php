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
        'title' => 'Closure in a namespace with use statements',
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

    'Global function call in the global scope' => <<<'PHP'
    <?php
    
    namespace Acme;
    
    use X\Foo;
    use X\Bar;
    use DateTimeImmutable;
    use Closure;
    
    function ($x) { return $x; };
    function (int $x) { return $x; };
    function (int $x): int { return $x; };
    function (Foo $x): Bar { return $x; };
    function (DateTimeImmutable $x): Closure { return $x; };
    
    static function ($x) { return $x; };
    static function (int $x) { return $x; };
    static function (int $x): int { return $x; };
    static function (Foo $x): Bar { return $x; };
    static function (DateTimeImmutable $x): Closure { return $x; };
    ----
    <?php
    
    namespace Humbug\Acme;
    
    use Humbug\X\Foo;
    use Humbug\X\Bar;
    use DateTimeImmutable;
    use Closure;
    function ($x) {
        return $x;
    };
    function (int $x) {
        return $x;
    };
    function (int $x) : int {
        return $x;
    };
    function (Foo $x) : Bar {
        return $x;
    };
    function (DateTimeImmutable $x) : Closure {
        return $x;
    };
    static function ($x) {
        return $x;
    };
    static function (int $x) {
        return $x;
    };
    static function (int $x) : int {
        return $x;
    };
    static function (Foo $x) : Bar {
        return $x;
    };
    static function (DateTimeImmutable $x) : Closure {
        return $x;
    };
    
    PHP,

    'Global function call in the global scope with global symbols exposed' => [
        'expose-global-classes' => true,
        'expose-global-functions' => true,
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme;
        
        use X\Foo;
        use X\Bar;
        use DateTimeImmutable;
        use Closure;
        
        function ($x) { return $x; };
        function (int $x) { return $x; };
        function (int $x): int { return $x; };
        function (Foo $x): Bar { return $x; };
        function (DateTimeImmutable $x): Closure { return $x; };
        
        static function ($x) { return $x; };
        static function (int $x) { return $x; };
        static function (int $x): int { return $x; };
        static function (Foo $x): Bar { return $x; };
        static function (DateTimeImmutable $x): Closure { return $x; };
        ----
        <?php
        
        namespace Humbug\Acme;
        
        use Humbug\X\Foo;
        use Humbug\X\Bar;
        use DateTimeImmutable;
        use Closure;
        function ($x) {
            return $x;
        };
        function (int $x) {
            return $x;
        };
        function (int $x) : int {
            return $x;
        };
        function (Foo $x) : Bar {
            return $x;
        };
        function (DateTimeImmutable $x) : Closure {
            return $x;
        };
        static function ($x) {
            return $x;
        };
        static function (int $x) {
            return $x;
        };
        static function (int $x) : int {
            return $x;
        };
        static function (Foo $x) : Bar {
            return $x;
        };
        static function (DateTimeImmutable $x) : Closure {
            return $x;
        };
        
        PHP,
    ],

    'Global function call in the global scope with exposed symbols' => [
        'expose-classes' => [
            'X\Foo',
            'X\Bar',
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme;
        
        use X\Foo;
        use X\Bar;
        use DateTimeImmutable;
        use Closure;
        
        function ($x) { return $x; };
        function (int $x) { return $x; };
        function (int $x): int { return $x; };
        function (Foo $x): Bar { return $x; };
        function (DateTimeImmutable $x): Closure { return $x; };
        
        static function ($x) { return $x; };
        static function (int $x) { return $x; };
        static function (int $x): int { return $x; };
        static function (Foo $x): Bar { return $x; };
        static function (DateTimeImmutable $x): Closure { return $x; };
        ----
        <?php
        
        namespace Humbug\Acme;
        
        use Humbug\X\Foo;
        use Humbug\X\Bar;
        use DateTimeImmutable;
        use Closure;
        function ($x) {
            return $x;
        };
        function (int $x) {
            return $x;
        };
        function (int $x) : int {
            return $x;
        };
        function (Foo $x) : Bar {
            return $x;
        };
        function (DateTimeImmutable $x) : Closure {
            return $x;
        };
        static function ($x) {
            return $x;
        };
        static function (int $x) {
            return $x;
        };
        static function (int $x) : int {
            return $x;
        };
        static function (Foo $x) : Bar {
            return $x;
        };
        static function (DateTimeImmutable $x) : Closure {
            return $x;
        };
        
        PHP,
    ],
];
