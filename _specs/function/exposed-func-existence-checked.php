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
        title: 'Exposed functions which are never declared but for which the existence is checked',
    ),

    'Non exposed global function call' => <<<'PHP'
        <?php

        function_exists('main');
        ----
        <?php

        namespace Humbug;

        \function_exists('Humbug\\main');

        PHP,

    'Exposed global function call' => SpecWithConfig::create(
        exposeFunctions: ['main'],
        spec: <<<'PHP'
            <?php

            function_exists('main');
            ----
            <?php

            namespace Humbug;

            \function_exists('Humbug\\main');

            PHP,
    ),

    'Global function call with exposed global functions' => SpecWithConfig::create(
        exposeGlobalFunctions: true,
        spec: <<<'PHP'
            <?php

            function_exists('main');
            ----
            <?php

            namespace Humbug;

            \function_exists('Humbug\\main');

            PHP,
    ),

    'Global function call with non-exposed global functions' => <<<'PHP'
        <?php

        function_exists('main');
        ----
        <?php

        namespace Humbug;

        \function_exists('Humbug\\main');

        PHP,

    'Exposed namespaced function call' => SpecWithConfig::create(
        exposeFunctions: ['Acme\main'],
        spec: <<<'PHP'
            <?php

            namespace Acme;

            function_exists('Acme\main');
            ----
            <?php

            namespace Humbug\Acme;

            function_exists('Humbug\\Acme\\main');

            PHP,
    ),

    'Namespaced function call from excluded namespace' => SpecWithConfig::create(
        excludeNamespaces: ['Acme'],
        spec: <<<'PHP'
            <?php

            namespace Acme;

            function_exists('Acme\main');
            ----
            <?php

            namespace Acme;

            function_exists('Acme\\main');

            PHP,
    ),
];
