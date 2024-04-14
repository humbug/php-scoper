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
        title: 'Single-level namespaced constant call in the global scope',
    ),

    'Namespaced constant call' => <<<'PHP'
        <?php

        PHPUnit\DUMMY_CONST;
        ----
        <?php

        namespace Humbug;

        \Humbug\PHPUnit\DUMMY_CONST;

        PHP,

    'FQ namespaced constant call' => <<<'PHP'
        <?php

        \PHPUnit\DUMMY_CONST;
        ----
        <?php

        namespace Humbug;

        \Humbug\PHPUnit\DUMMY_CONST;

        PHP,

    'Namespaced constant call on an exposed constant' => SpecWithConfig::create(
        exposeConstants: ['PHPUnit\DUMMY_CONST'],
        spec: <<<'PHP'
            <?php

            PHPUnit\DUMMY_CONST;
            ----
            <?php

            namespace Humbug;

            \PHPUnit\DUMMY_CONST;

            PHP,
    ),
];
