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
        title: 'Global function call in the global scope',
    ),

    'Global function call in the global scope' => <<<'PHP'
        <?php

        main();
        ----
        <?php

        namespace Humbug;

        main();

        PHP,

    'Uppercase global function call in the global scope' => <<<'PHP'
        <?php

        MAIN();
        ----
        <?php

        namespace Humbug;

        MAIN();

        PHP,

    'FQ global function call in the global scope' => <<<'PHP'
        <?php

        \main();
        ----
        <?php

        namespace Humbug;

        \Humbug\main();

        PHP,

    'Global function call in the global scope of an internal function' => <<<'PHP'
        <?php

        is_array();
        ----
        <?php

        namespace Humbug;

        \is_array();

        PHP,

    'Uppercase global function call in the global scope of an internal function' => <<<'PHP'
        <?php

        IS_ARRAY();
        ----
        <?php

        namespace Humbug;

        \IS_ARRAY();

        PHP,

    'FQ global function call in the global scope of an internal function' => <<<'PHP'
        <?php

        \is_array();
        ----
        <?php

        namespace Humbug;

        \is_array();

        PHP,

    'Uppercase FQ global function call in the global scope of an internal function' => <<<'PHP'
        <?php

        \IS_ARRAY();
        ----
        <?php

        namespace Humbug;

        \IS_ARRAY();

        PHP,

    'Global function call in the global scope of a function which has a use statement for a class importing a class with the same name' => <<<'PHP'
        <?php

        use Acme\Glob;

        glob();
        ----
        <?php

        namespace Humbug;

        use Humbug\Acme\Glob;
        \glob();

        PHP,
];
