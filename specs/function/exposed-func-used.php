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
        title: 'Exposed functions which are never declared',

















    ),

    'Non exposed global function call' => <<<'PHP'
        <?php

        main();
        ----
        <?php

        namespace Humbug;

        main();

        PHP,

    'Exposed global function call' => [
        exposeFunctions: ['main'],
        expectedRecordedFunctions: [
            ['main', 'Humbug\main'],
        ],
        'payload' => <<<'PHP'
            <?php

            main();
            ----
            <?php

            namespace Humbug;

            \Humbug\main();

            PHP,
    ],

    'Global function call with exposed global functions' => [
        exposeGlobalFunctions: true,
        expectedRecordedFunctions: [
            ['main', 'Humbug\main'],
        ],
        'payload' => <<<'PHP'
            <?php

            main();
            ----
            <?php

            namespace Humbug;

            main();

            PHP,
    ],

    'Global function call with non-exposed global functions' => <<<'PHP'
        <?php

        main();
        ----
        <?php

        namespace Humbug;

        main();

        PHP,

    'Exposed namespaced function call' => [
        exposeFunctions: ['Acme\main'],
          // Nothing registered here since the FQ could not be resolved
        'payload' => <<<'PHP'
            <?php

            namespace Acme;

            main();
            ----
            <?php

            namespace Humbug\Acme;

            main();

            PHP,
    ],
];
