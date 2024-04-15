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
use Humbug\PhpScoper\SpecFramework\Config\SpecWithConfig;

return [
    'meta' => new Meta(
        title: 'Functions for the fully-qualified class name could not be resolved with expose global functions enabled',
        exposeGlobalFunctions: true,
    ),

    'Function call in namespace without a use statement' => <<<'PHP'
        <?php

        namespace Acme;

        main();
        ----
        <?php

        namespace Humbug\Acme;

        main();

        PHP,

    'Internal function call in namespace without a use statement' => <<<'PHP'
        <?php

        namespace Acme;

        array_values();
        ----
        <?php

        namespace Humbug\Acme;

        array_values();

        PHP,

    'Excluded function call in namespace without a use statement' => SpecWithConfig::create(
        excludeFunctions: ['main'],
        spec: <<<'PHP'
            <?php

            namespace Acme;

            main();
            ----
            <?php

            namespace Humbug\Acme;

            main();

            PHP,
    ),

    'Exposed namespaced function call' => SpecWithConfig::create(
        exposeFunctions: ['Acme\main'],
        spec: <<<'PHP'
            <?php

            namespace Acme;

            main();
            ----
            <?php

            namespace Humbug\Acme;

            main();

            PHP,
    ),

    'Namespaced function call belonging to an exposed namespace' => SpecWithConfig::create(
        exposeNamespaces: ['Acme'],
        spec: <<<'PHP'
            <?php

            namespace Acme;

            main();
            ----
            <?php

            namespace Humbug\Acme;

            main();

            PHP,
    ),
];
