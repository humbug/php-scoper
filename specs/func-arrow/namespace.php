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
        'minPhpVersion' => 70400,
        'title' => 'Arrow function in a namespace',
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
    
    fn ($x) => $x;
    fn (int $x) => $x;
    fn (int $x): int => $x;
    fn (Foo $x): Bar => $x;
    fn (DateTimeImmutable $x): Closure => $x;
    ----
    <?php
    
    namespace Humbug\Acme;
    
    fn($x) => $x;
    fn(int $x) => $x;
    fn(int $x): int => $x;
    fn(Foo $x): Bar => $x;
    fn(DateTimeImmutable $x): Closure => $x;
    
    PHP,

    'Global function call in the global scope with global symbols exposed' => [
        'expose-global-classes' => true,
        'expose-global-functions' => true,
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme;
        
        fn ($x) => $x;
        fn (int $x) => $x;
        fn (int $x): int => $x;
        fn (Foo $x): Bar => $x;
        fn (DateTimeImmutable $x): Closure => $x;
        ----
        <?php
        
        namespace Humbug\Acme;
        
        fn($x) => $x;
        fn(int $x) => $x;
        fn(int $x): int => $x;
        fn(Foo $x): Bar => $x;
        fn(DateTimeImmutable $x): Closure => $x;
        
        PHP,
    ],

    'Global function call in the global scope with exposed symbols' => [
        'expose-classes' => [
            'Acme\Foo',
            'Acme\Bar',
            'Acme\Humbug\Acme\DateTimeImmutable',
            'Acme\Humbug\Acme\Closure',
        ],
        'payload' => <<<'PHP'
        <?php
        
        namespace Acme;
        
        fn ($x) => $x;
        fn (int $x) => $x;
        fn (int $x): int => $x;
        fn (Foo $x): Bar => $x;
        fn (DateTimeImmutable $x): Closure => $x;
        ----
        <?php
        
        namespace Humbug\Acme;
        
        fn($x) => $x;
        fn(int $x) => $x;
        fn(int $x): int => $x;
        fn(Foo $x): Bar => $x;
        fn(DateTimeImmutable $x): Closure => $x;
        
        PHP,
    ],
];
