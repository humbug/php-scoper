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
        title: 'Global function call imported with an aliased use statement in the global scope',
    ),

    'Global function call imported with a use statement in the global scope' => <<<'PHP'
        <?php

        use function main as foo;

        foo();
        ----
        <?php

        namespace Humbug;

        use function Humbug\main as foo;
        foo();

        PHP,

    'Global function call imported with a use statement in the global scope with global functions exposed' => SpecWithConfig::create(
        exposeGlobalFunctions: true,
        expectedRecordedFunctions: [
            ['main', 'Humbug\main'],
        ],
        spec: <<<'PHP'
            <?php

            use function main as foo;

            foo();
            ----
            <?php

            namespace Humbug;

            use function Humbug\main as foo;
            foo();

            PHP,
    ),

    'Global FQ function call imported with a use statement in the global scope' => <<<'PHP'
        <?php

        use function main as foo;

        \foo();
        ----
        <?php

        namespace Humbug;

        use function Humbug\main as foo;
        \Humbug\foo();

        PHP,

    'Global FQ function call imported with a use statement in the global scope with global functions exposed' => SpecWithConfig::create(
        exposeGlobalFunctions: true,
        expectedRecordedFunctions: [
            ['foo', 'Humbug\foo'],
        ],
        spec: <<<'PHP'
            <?php

            use function main as foo;

            \foo();
            ----
            <?php

            namespace Humbug;

            use function Humbug\main as foo;
            \Humbug\foo();

            PHP,
    ),
];
