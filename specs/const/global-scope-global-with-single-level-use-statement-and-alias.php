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
        title: 'Global constant imported with an aliased use statement used in the global scope',

















    ),

    'Constant call imported with an aliased use statement' => <<<'PHP'
        <?php

        use const DUMMY_CONST as FOO;

        FOO;
        ----
        <?php

        namespace Humbug;

        use const Humbug\DUMMY_CONST as FOO;
        FOO;

        PHP,

    'Exposed constant call imported with an aliased use statement' => [
        exposeConstants: ['DUMMY_CONST'],
        'payload' => <<<'PHP'
            <?php

            use const DUMMY_CONST as FOO;

            FOO;
            ----
            <?php

            namespace Humbug;

            use const DUMMY_CONST as FOO;
            FOO;

            PHP,
    ],

    'Constant FQ call imported with an aliased use statement' => <<<'PHP'
        <?php

        use const DUMMY_CONST as FOO;

        \FOO;
        ----
        <?php

        namespace Humbug;

        use const Humbug\DUMMY_CONST as FOO;
        \Humbug\FOO;

        PHP,
];
