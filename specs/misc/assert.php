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
        title: 'Ensures the function `assert()` cannot be exposed in any way (it is a reserved symbol).',
    ),

    'usage in the global namespace' => <<<'PHP'
        <?php

        assert();
        \assert();

        ----
        <?php

        namespace Humbug;

        \assert();
        \assert();

        PHP,

    'usage in the global namespace whilst exposed' => SpecWithConfig::create(
        exposeFunctions: ['assert'],
        spec: <<<'PHP'
            <?php

            assert();
            \assert();

            ----
            <?php

            namespace Humbug;

            \assert();
            \assert();

            PHP,
    ),

    'usage in the global namespace whilst excluded' => SpecWithConfig::create(
        excludeFunctions: ['assert'],
        spec: <<<'PHP'
            <?php

            assert();
            \assert();

            ----
            <?php

            namespace Humbug;

            \assert();
            \assert();

            PHP,
    ),

    'usage in a namespace' => <<<'PHP'
        <?php

        namespace Box;

        assert();
        \assert();

        namespace PhpScoper;

        use function assert;

        assert();
        \assert();

        ----
        <?php

        namespace Humbug\Box;

        assert();
        \assert();
        namespace Humbug\PhpScoper;

        use function assert;
        assert();
        \assert();

        PHP,

    'usage in a namespace whilst exposed' => SpecWithConfig::create(
        exposeFunctions: ['assert'],
        spec: <<<'PHP'
            <?php

            namespace Box;

            assert();
            \assert();

            namespace PhpScoper;

            use function assert;

            assert();
            \assert();

            ----
            <?php

            namespace Humbug\Box;

            assert();
            \assert();
            namespace Humbug\PhpScoper;

            use function assert;
            assert();
            \assert();

            PHP,
    ),

    'usage in a namespace whilst excluded' => SpecWithConfig::create(
        excludeFunctions: ['assert'],
        spec: <<<'PHP'
            <?php

            namespace Box;

            assert();
            \assert();

            namespace PhpScoper;

            use function assert;

            assert();
            \assert();

            ----
            <?php

            namespace Humbug\Box;

            assert();
            \assert();
            namespace Humbug\PhpScoper;

            use function assert;
            assert();
            \assert();

            PHP,
    ),
];
