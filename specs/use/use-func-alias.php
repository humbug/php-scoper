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
        title: 'Use statements for functions',
    ),

    'Use statement for a function belonging to the global namespace' => <<<'PHP'
        <?php

        use function foo as greet;

        ----
        <?php

        namespace Humbug;

        use function Humbug\foo as greet;

        PHP,

    'Use statement for a function belonging to the global namespace which has already been prefixed' => <<<'PHP'
        <?php

        use function Humbug\foo as greet;

        ----
        <?php

        namespace Humbug;

        use function Humbug\foo as greet;

        PHP,

    'Use statement for a namespaced function' => <<<'PHP'
        <?php

        use function Foo\bar as greet;

        ----
        <?php

        namespace Humbug;

        use function Humbug\Foo\bar as greet;

        PHP,

    'Use statement for a namespaced function which has already been prefixed' => <<<'PHP'
        <?php

        use function Humbug\Foo\bar as greet;

        ----
        <?php

        namespace Humbug;

        use function Humbug\Foo\bar as greet;

        PHP,

    'Use statement for a namespaced function which has been exposed' => SpecWithConfig::create(
        exposeFunctions: ['Foo\bar'],
        spec: <<<'PHP'
            <?php

            use function Foo\bar as greet;

            ----
            <?php

            namespace Humbug;

            use function Humbug\Foo\bar as greet;

            PHP,
    ),
];
