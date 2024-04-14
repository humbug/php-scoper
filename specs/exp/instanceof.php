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
        title: 'Instanceof expressions',
    ),

    'Instance of an internal class' => <<<'PHP'
        <?php

        $x = new stdClass();
        $x instanceof stdClass;
        ----
        <?php

        namespace Humbug;

        $x = new \stdClass();
        $x instanceof \stdClass;

        PHP,

    'Instance of an internal class in a namespace' => <<<'PHP'
        <?php

        namespace Acme;

        use stdClass;

        $x = new stdClass();
        $x instanceof stdClass;

        ----
        <?php

        namespace Humbug\Acme;

        use stdClass;
        $x = new stdClass();
        $x instanceof stdClass;

        PHP,

    'Instance of a custom exception class' => <<<'PHP'
        <?php

        $x = new Foo();
        $x instanceof Foo;

        ----
        <?php

        namespace Humbug;

        $x = new Foo();
        $x instanceof Foo;

        PHP,

    'Instance of a custom exception class in a namespace' => <<<'PHP'
        <?php

        namespace Acme;

        $x = new Foo();
        $x instanceof Foo;

        ----
        <?php

        namespace Humbug\Acme;

        $x = new Foo();
        $x instanceof Foo;

        PHP,

    'Instance of with ternary' => <<<'PHP'
        <?php

        namespace Acme;

        $file = new \stdClass();

        $file instanceof \SplFileInfo ? $file : new \SplFileInfo($file);

        ----
        <?php

        namespace Humbug\Acme;

        $file = new \stdClass();
        $file instanceof \SplFileInfo ? $file : new \SplFileInfo($file);

        PHP,

    'Instance of with special keyword' => <<<'PHP'
        <?php

        namespace Acme;

        $file instanceof static;
        $file instanceof self;
        $file instanceof parent;

        ----
        <?php

        namespace Humbug\Acme;

        $file instanceof static;
        $file instanceof self;
        $file instanceof parent;

        PHP,
];
