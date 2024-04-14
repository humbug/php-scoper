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
        title: 'Namespace declarations without braces',
    ),

    'Root namespace' => <<<'PHP'
        <?php

        namespace {
            $x = 'root';
        }

        ----
        <?php

        namespace Humbug;

        $x = 'root';

        PHP,

    'One level' => <<<'PHP'
        <?php

        namespace Foo {
        }

        ----
        <?php

        namespace Humbug\Foo;


        PHP,

    'Already prefixed one-level namespace' => <<<'PHP'
        <?php

        namespace Humbug\Foo {
        }

        ----
        <?php

        namespace Humbug\Foo;


        PHP,

    'Two levels namespace' => <<<'PHP'
        <?php

        namespace Foo\Bar {
        }

        ----
        <?php

        namespace Humbug\Foo\Bar;


        PHP,

    'Already prefixed two levels namespace' => <<<'PHP'
        <?php

        namespace Humbug\Foo\Bar;

        ----
        <?php

        namespace Humbug\Foo\Bar;


        PHP,

    'Mix' => <<<'PHP'
        <?php

        // single level
        namespace A {
            $x = 'a';
        }

        // single level
        namespace B {
            $x = 'b';
        }

        // already prefixed one level
        namespace Humbug\C {
            $x = 'pa';
        }

        // two levels
        namespace D\E {
            $x = 'de';
        }

        // two levels
        namespace F\G {
            $x = 'fg';
        }

        // already prefixed two levels
        namespace Humbug\H\I {
            $x = 'phi';
        }

        // root namespace
        namespace {
            $x = 'root';
        }

        ----
        <?php

        // single level
        namespace Humbug\A;

        $x = 'a';
        // single level
        namespace Humbug\B;

        $x = 'b';
        // already prefixed one level
        namespace Humbug\C;

        $x = 'pa';
        // two levels
        namespace Humbug\D\E;

        $x = 'de';
        // two levels
        namespace Humbug\F\G;

        $x = 'fg';
        // already prefixed two levels
        namespace Humbug\H\I;

        $x = 'phi';
        // root namespace
        namespace Humbug;

        $x = 'root';

        PHP,
];
