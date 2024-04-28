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
        title: 'Namespaced function call statement in a namespace',
        exposeGlobalConstants: true,
    ),

    'Namespaced function call' => <<<'PHP'
        <?php

        namespace X;

        PHPUnit\main();
        ----
        <?php

        namespace Humbug\X;

        PHPUnit\main();

        PHP,

    'FQ namespaced function call' => <<<'PHP'
        <?php

        namespace X;

        \PHPUnit\main();
        ----
        <?php

        namespace Humbug\X;

        \Humbug\PHPUnit\main();

        PHP,

    'Exposed namespaced function call' => SpecWithConfig::create(
        exposeFunctions: ['PHPUnit\X\main'],
        // No function registered to the recorded symbols here since no FQ could be resolved
        spec: <<<'PHP'
            <?php

            namespace X;

            PHPUnit\main();
            ----
            <?php

            namespace Humbug\X;

            PHPUnit\main();

            PHP,
    ),

    'FQ exposed namespaced function call' => SpecWithConfig::create(
        exposeFunctions: ['PHPUnit\main'],
        expectedRecordedFunctions: [
            ['PHPUnit\main', 'Humbug\PHPUnit\main'],
        ],
        spec: <<<'PHP'
            <?php

            namespace X;

            \PHPUnit\main();
            ----
            <?php

            namespace Humbug\X;

            \Humbug\PHPUnit\main();

            PHP,
    ),
];
