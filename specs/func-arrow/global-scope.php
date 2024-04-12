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

use Humbug\PhpScoper\Scoper\Spec\Meta;

return [
    'meta' => new Meta(
        'minPhpVersion' => 70400,
        title: 'Arrow function in the global namespace',

















    ),

    'Global function call in the global scope' => <<<'PHP'
    <?php
    
    fn ($x) => $x;
    fn (int $x) => $x;
    fn (int $x): int => $x;
    fn (Foo $x): Bar => $x;
    fn (DateTimeImmutable $x): Closure => $x;
    ----
    <?php
    
    namespace Humbug;
    
    fn($x) => $x;
    fn(int $x) => $x;
    fn(int $x): int => $x;
    fn(Foo $x): Bar => $x;
    fn(\DateTimeImmutable $x): \Closure => $x;
    
    PHP,

    'Global function call in the global scope with global symbols exposed' => [
        exposeGlobalClasses: true,
        exposeGlobalFunctions: true,
        'payload' => <<<'PHP'
        <?php
        
        fn ($x) => $x;
        fn (int $x) => $x;
        fn (int $x): int => $x;
        fn (Foo $x): Bar => $x;
        fn (DateTimeImmutable $x): Closure => $x;
        ----
        <?php
        
        namespace Humbug;
        
        fn($x) => $x;
        fn(int $x) => $x;
        fn(int $x): int => $x;
        fn(Foo $x): Bar => $x;
        fn(\DateTimeImmutable $x): \Closure => $x;
        
        PHP,
    ],

    'Global function call in the global scope with exposed symbols' => [
        exposeClasses: [
            'Foo',
            'Bar',
        ],
        'payload' => <<<'PHP'
        <?php
        
        fn ($x) => $x;
        fn (int $x) => $x;
        fn (int $x): int => $x;
        fn (Foo $x): Bar => $x;
        fn (DateTimeImmutable $x): Closure => $x;
        ----
        <?php
        
        namespace Humbug;
        
        fn($x) => $x;
        fn(int $x) => $x;
        fn(int $x): int => $x;
        fn(\Humbug\Foo $x): \Humbug\Bar => $x;
        fn(\DateTimeImmutable $x): \Closure => $x;
        
        PHP,
    ],
];
