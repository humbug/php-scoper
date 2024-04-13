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
        title: 'Global constant usage in the global scope',
    ),

    'Constant call in the global namespace' => <<<'PHP'
        <?php

        DUMMY_CONST;
        ----
        <?php

        namespace Humbug;

        \Humbug\DUMMY_CONST;

        PHP,

    'Exposed constant call in the global namespace' => SpecWithConfig::create(
        exposeConstants: ['DUMMY_CONST'],
        spec: <<<'PHP'
            <?php

            DUMMY_CONST;
            ----
            <?php

            namespace Humbug;

            \DUMMY_CONST;

            PHP,
    ),

    'Constant call in the global namespace which is excluded' => SpecWithConfig::create(
        excludeNamespaces: [''],
        spec: <<<'PHP'
            <?php

            DUMMY_CONST;
            ----
            <?php

            namespace {
                \DUMMY_CONST;
            }

            PHP,
    ),

    'Internal constant call in the global namespace' => <<<'PHP'
        <?php

        DIRECTORY_SEPARATOR;
        ----
        <?php

        namespace Humbug;

        \DIRECTORY_SEPARATOR;

        PHP,

    'FQ constant call in the global namespace' => <<<'PHP'
        <?php

        DUMMY_CONST;
        ----
        <?php

        namespace Humbug;

        \Humbug\DUMMY_CONST;

        PHP,

    'Global constant call in the global scope of a constant which has a use statement for a class importing a class with the same name' => <<<'PHP'
        <?php

        use Acme\Inf;

        INF;
        ----
        <?php

        namespace Humbug;

        use Humbug\Acme\Inf;
        \INF;

        PHP,
];
