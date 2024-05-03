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

use Humbug\PhpScoper\SpecFramework\Config\Meta;

return [
    'meta' => new Meta(
        title: 'Union types',
    ),

    'Method casts' => <<<'PHP'
        <?php

        class X
        {
            public function method1(Y|Z $a, null|Y $b): null|Y|Z
            {
            }
            public function method2(?Y $b): ?Z
            {
            }
            public function method3(self|null $b): static|null
            {
            }
        }

        ----
        <?php

        namespace Humbug;

        class X
        {
            public function method1(Y|Z $a, null|Y $b): null|Y|Z
            {
            }
            public function method2(?Y $b): ?Z
            {
            }
            public function method3(self|null $b): static|null
            {
            }
        }

        PHP,

    'Function casts' => <<<'PHP'
        <?php

        function fun1(Y|Z $a, null|Y $b): null|Y|Z
        {
        }
        function fun2(?Y $b): ?Z
        {
        }

        ----
        <?php

        namespace Humbug;

        function fun1(Y|Z $a, null|Y $b): null|Y|Z
        {
        }
        function fun2(?Y $b): ?Z
        {
        }

        PHP,

    'Property casts' => <<<'PHP'
        <?php

        class X
        {
            private null|Y|Z $x;
            private ?X $y;
            private null|self $z;
        }

        ----
        <?php

        namespace Humbug;

        class X
        {
            private null|Y|Z $x;
            private ?X $y;
            private null|self $z;
        }

        PHP,

    'Trait casts' => <<<'PHP'
        <?php

        trait X
        {
            private null|Y|Z $x;
            private ?X $y;
            private null|self $z;
            public function method1(Y|Z $a, null|Y $b): null|Y|Z
            {
            }
            public function method2(?Y $b): ?Z
            {
            }
            public function method3(self|null $b): static|null
            {
            }
        }

        ----
        <?php

        namespace Humbug;

        trait X
        {
            private null|Y|Z $x;
            private ?X $y;
            private null|self $z;
            public function method1(Y|Z $a, null|Y $b): null|Y|Z
            {
            }
            public function method2(?Y $b): ?Z
            {
            }
            public function method3(self|null $b): static|null
            {
            }
        }

        PHP,

    'Interface casts' => <<<'PHP'
        <?php

        interface X
        {
            public function method1(Y|Z $a, null|Y $b): null|Y|Z;
            public function method2(?Y $b): ?Z;
            public function method3(self|null $b): static|null;
        }

        ----
        <?php

        namespace Humbug;

        interface X
        {
            public function method1(Y|Z $a, null|Y $b): null|Y|Z;
            public function method2(?Y $b): ?Z;
            public function method3(self|null $b): static|null;
        }

        PHP,

    'Untouched scalar casts' => <<<'PHP'
        <?php

        interface X
        {
            public function method1(string|int $b): string|int;
        }

        ----
        <?php

        namespace Humbug;

        interface X
        {
            public function method1(string|int $b): string|int;
        }

        PHP,
];
