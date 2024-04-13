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
        title: 'Global constant usage in a namespace with the global constants exposed',
        exposeGlobalConstants: true,
    ),

    'Constant call in a namespace' => <<<'PHP'
        <?php

        namespace A;

        DUMMY_CONST;
        ----
        <?php

        namespace Humbug\A;

        DUMMY_CONST;

        PHP,

    'Exposed constant call in a namespace' => [
        'expose-constants' => ['DUMMY_CONST'],
        'payload' => <<<'PHP'
            <?php

            namespace A;

            DUMMY_CONST;
            ----
            <?php

            namespace Humbug\A;

            DUMMY_CONST;

            PHP,
    ],

    'FQ constant call in a namespace' => <<<'PHP'
        <?php

        namespace A;

        \DUMMY_CONST;
        ----
        <?php

        namespace Humbug\A;

        \DUMMY_CONST;

        PHP,

    'Exposed FQ constant call in a namespace' => [
        'expose-constants' => ['DUMMY_CONST'],
        'payload' => <<<'PHP'
            <?php

            namespace A;

            \DUMMY_CONST;
            ----
            <?php

            namespace Humbug\A;

            \DUMMY_CONST;

            PHP,
    ],
];
