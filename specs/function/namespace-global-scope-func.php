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
        title: 'global function call in a namespace',
        exposeGlobalConstants: true,
    ),

    // We don't do anything as there is no ways to distinguish between a namespaced function call
    // from the same namespace and a function registered in the global scope
    'single-part' => SpecWithConfig::create(
        exposeFunctions: ['foo'],
        expectedRecordedAmbiguousFunctions: [
            ['main', 'Humbug\main'],
        ],
        spec: <<<'PHP'
            <?php

            namespace X;

            main();
            ----
            <?php

            namespace Humbug\X;

            main();

            PHP,
    ),

    'FQ single-part' => <<<'PHP'
        <?php

        namespace X;

        \main();
        ----
        <?php

        namespace Humbug\X;

        \Humbug\main();

        PHP,

    'Exposed constant call in a namespace' => SpecWithConfig::create(
        exposeFunctions: ['foo'],
        expectedRecordedAmbiguousFunctions: [
            ['foo', 'Humbug\foo'],
        ],
        spec: <<<'PHP'
            <?php

            namespace A;

            foo();
            ----
            <?php

            namespace Humbug\A;

            foo();

            PHP,
    ),
];
