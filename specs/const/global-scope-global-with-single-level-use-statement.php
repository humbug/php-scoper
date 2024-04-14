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
        title: 'Global constant imported with a use statement used in the global scope',
        exposeGlobalFunctions: true,
    ),

    'Constant call imported with a use statement' => <<<'PHP'
        <?php

        use const DUMMY_CONST;

        DUMMY_CONST;
        ----
        <?php

        namespace Humbug;

        use const Humbug\DUMMY_CONST;
        DUMMY_CONST;

        PHP,

    'Exposed constant call imported with a use statement' => SpecWithConfig::create(
        exposeConstants: ['DUMMY_CONST'],
        spec: <<<'PHP'
            <?php

            use const DUMMY_CONST;

            DUMMY_CONST;
            ----
            <?php

            namespace Humbug;

            use const DUMMY_CONST;
            DUMMY_CONST;

            PHP,
    ),

    'FQ constant call imported with a use statement' => <<<'PHP'
        <?php

        use const DUMMY_CONST;

        \DUMMY_CONST;
        ----
        <?php

        namespace Humbug;

        use const Humbug\DUMMY_CONST;
        \Humbug\DUMMY_CONST;

        PHP,
];
