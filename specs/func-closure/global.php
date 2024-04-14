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
use Humbug\PhpScoper\Scoper\Spec\SpecWithConfig;

return [
    'meta' => new Meta(
        title: 'Closure in the global namespace',
    ),

    'Global function call in the global scope' => <<<'PHP'
        <?php

        function ($x) { return $x; };
        function (int $x): int { return $x; };
        function (Foo $x): Bar { return $x; };
        function (DateTimeImmutable $x): Closure { return $x; };

        static function ($x) { return $x; };
        static function (int $x): int { return $x; };
        static function (Foo $x): Bar { return $x; };
        static function (DateTimeImmutable $x): Closure { return $x; };
        ----
        <?php

        namespace Humbug;

        function ($x) {
            return $x;
        };
        function (int $x) : int {
            return $x;
        };
        function (Foo $x) : Bar {
            return $x;
        };
        function (\DateTimeImmutable $x) : \Closure {
            return $x;
        };
        static function ($x) {
            return $x;
        };
        static function (int $x) : int {
            return $x;
        };
        static function (Foo $x) : Bar {
            return $x;
        };
        static function (\DateTimeImmutable $x) : \Closure {
            return $x;
        };

        PHP,

    'Global function call in the global scope with global symbols exposed' => SpecWithConfig::create(
        exposeGlobalClasses: true,
        exposeGlobalFunctions: true,
        spec: <<<'PHP'
            <?php

            function ($x) {return $x; };
            function (int $x) {return $x; };
            function (int $x): int {return $x; };
            function (Foo $x): Bar {return $x; };
            function (DateTimeImmutable $x): Closure {return $x; };

            static function ($x) {return $x; };
            static function (int $x) {return $x; };
            static function (int $x): int {return $x; };
            static function (Foo $x): Bar {return $x; };
            static function (DateTimeImmutable $x): Closure {return $x; };
            ----
            <?php

            namespace Humbug;

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
            function (\DateTimeImmutable $x) : \Closure {
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
            static function (\DateTimeImmutable $x) : \Closure {
                return $x;
            };

            PHP,
    ),

    'Global function call in the global scope with exposed symbols' => SpecWithConfig::create(
        exposeClasses: [
            'Foo',
            'Bar',
        ],
        spec: <<<'PHP'
            <?php

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

            namespace Humbug;

            function ($x) {
                return $x;
            };
            function (int $x) {
                return $x;
            };
            function (int $x) : int {
                return $x;
            };
            function (\Humbug\Foo $x) : \Humbug\Bar {
                return $x;
            };
            function (\DateTimeImmutable $x) : \Closure {
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
            static function (\Humbug\Foo $x) : \Humbug\Bar {
                return $x;
            };
            static function (\DateTimeImmutable $x) : \Closure {
                return $x;
            };

            PHP,
    ),
];
