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
        title: 'Global function call in a namespace',
    ),

    'Global function call in a namespace' => SpecWithConfig::create(
        expectedRecordedAmbiguousFunctions: [
            ['main', 'Humbug\main'],
        ],
        spec: <<<'PHP'
            <?php

            namespace A;

            main();
            ----
            <?php

            namespace Humbug\A;

            main();

            PHP,
    ),

    'Global FQ function call in a namespace' => <<<'PHP'
        <?php

        namespace A;

        \main();
        ----
        <?php

        namespace Humbug\A;

        \Humbug\main();

        PHP,
];
