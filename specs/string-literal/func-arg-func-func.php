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

return [
    'meta' => [
        'title' => 'String literal used as a function argument of function-related functions',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',

        'expose-global-constants' => false,
        'expose-global-classes' => false,
        'expose-global-functions' => false,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'expected-recorded-classes' => [],
        'expected-recorded-functions' => [],
    ],

    'FQFN string argument' => <<<'PHP'
        <?php

        function_exists('Acme\foo');
        function_exists('\\Acme\\foo');
        function_exists('Humbug\\Acme\\foo');
        function_exists('\\Humbug\\Acme\\foo');

        function_exists('dump');
        function_exists('\\dump');
        function_exists('Humbug\\dump');
        function_exists('\\Humbug\\dump');

        function_exists('var_dump');
        function_exists('\\var_dump');
        function_exists('Humbug\\var_dump');
        function_exists('\\Humbug\\var_dump');

        ----
        <?php

        namespace Humbug;

        \function_exists('Humbug\\Acme\\foo');
        \function_exists('Humbug\\Acme\\foo');
        \function_exists('Humbug\\Acme\\foo');
        \function_exists('Humbug\\Acme\\foo');
        \function_exists('Humbug\\dump');
        \function_exists('Humbug\\dump');
        \function_exists('Humbug\\dump');
        \function_exists('Humbug\\dump');
        \function_exists('var_dump');
        \function_exists('\\var_dump');
        \function_exists('Humbug\\var_dump');
        \function_exists('Humbug\\var_dump');

        PHP,

    'FQFN string argument on exposed function' => [
        'expose-functions' => ['Acme\foo', 'dump'],
        'expected-recorded-functions' => [
            ['Acme\foo', 'Humbug\Acme\foo'],
            ['dump', 'Humbug\dump'],
        ],
        'payload' => <<<'PHP'
            <?php

            function_exists('Acme\foo');
            function_exists('\\Acme\\foo');
            function_exists('Humbug\\Acme\\foo');
            function_exists('\\Humbug\\Acme\\foo');

            function_exists('dump');
            function_exists('\\dump');
            function_exists('Humbug\\dump');
            function_exists('\\Humbug\\dump');

            function_exists('var_dump');
            function_exists('\\var_dump');
            function_exists('Humbug\\var_dump');
            function_exists('\\Humbug\\var_dump');

            ----
            <?php

            namespace Humbug;

            \function_exists('Humbug\\Acme\\foo');
            \function_exists('Humbug\\Acme\\foo');
            \function_exists('Humbug\\Acme\\foo');
            \function_exists('Humbug\\Acme\\foo');
            \function_exists('Humbug\\dump');
            \function_exists('Humbug\\dump');
            \function_exists('Humbug\\dump');
            \function_exists('Humbug\\dump');
            \function_exists('var_dump');
            \function_exists('\\var_dump');
            \function_exists('Humbug\\var_dump');
            \function_exists('Humbug\\var_dump');

            PHP,
    ],

    'FQFN string argument on function from an excluded namespace' => [
        'exclude-namespaces' => [
            'Acme',
            '/^$/',
        ],
        'payload' => <<<'PHP'
            <?php

            function_exists('Acme\foo');
            function_exists('\\Acme\\foo');
            function_exists('Humbug\\Acme\\foo');
            function_exists('\\Humbug\\Acme\\foo');

            function_exists('dump');
            function_exists('\\dump');
            function_exists('Humbug\\dump');
            function_exists('\\Humbug\\dump');

            function_exists('var_dump');
            function_exists('\\var_dump');
            function_exists('Humbug\\var_dump');
            function_exists('\\Humbug\\var_dump');

            ----
            <?php

            namespace {
                \function_exists('Acme\\foo');
                \function_exists('\\Acme\\foo');
                \function_exists('Humbug\\Acme\\foo');
                \function_exists('\\Humbug\\Acme\\foo');
                \function_exists('dump');
                \function_exists('\\dump');
                \function_exists('Humbug\\dump');
                \function_exists('Humbug\\dump');
                \function_exists('var_dump');
                \function_exists('\\var_dump');
                \function_exists('Humbug\\var_dump');
                \function_exists('Humbug\\var_dump');
            }

            PHP,
    ],

    'FQFN string argument with global functions exposed' => [
        'expose-global-functions' => true,
        'expected-recorded-functions' => [
            ['dump', 'Humbug\dump'],
        ],
        'payload' => <<<'PHP'
            <?php

            function_exists('Acme\foo');
            function_exists('\\Acme\\foo');
            function_exists('Humbug\\Acme\\foo');
            function_exists('\\Humbug\\Acme\\foo');

            function_exists('dump');
            function_exists('\\dump');
            function_exists('Humbug\\dump');
            function_exists('\\Humbug\\dump');

            function_exists('var_dump');
            function_exists('\\var_dump');
            function_exists('Humbug\\var_dump');
            function_exists('\\Humbug\\var_dump');

            ----
            <?php

            namespace Humbug;

            \function_exists('Humbug\\Acme\\foo');
            \function_exists('Humbug\\Acme\\foo');
            \function_exists('Humbug\\Acme\\foo');
            \function_exists('Humbug\\Acme\\foo');
            \function_exists('Humbug\\dump');
            \function_exists('Humbug\\dump');
            \function_exists('Humbug\\dump');
            \function_exists('Humbug\\dump');
            \function_exists('var_dump');
            \function_exists('\\var_dump');
            \function_exists('Humbug\\var_dump');
            \function_exists('Humbug\\var_dump');

            PHP,
    ],

    'FQCN string argument formed by concatenated strings' => <<<'PHP'
        <?php

        function_exists('Acme\foo'.'');
        function_exists('\\Acme\\foo'.'');
        function_exists('Humbug\\Acme\\foo'.'');
        function_exists('\\Humbug\\Acme\\foo'.'');

        function_exists('dump'.'');
        function_exists('\\dump'.'');
        function_exists('Humbug\\dump'.'');
        function_exists('\\Humbug\\dump'.'');

        function_exists('var_dump'.'');
        function_exists('\\var_dump'.'');
        function_exists('Humbug\\var_dump'.'');
        function_exists('\\Humbug\\var_dump'.'');

        ----
        <?php

        namespace Humbug;

        \function_exists('Acme\\foo' . '');
        \function_exists('\\Acme\\foo' . '');
        \function_exists('Humbug\\Acme\\foo' . '');
        \function_exists('\\Humbug\\Acme\\foo' . '');
        \function_exists('dump' . '');
        \function_exists('\\dump' . '');
        \function_exists('Humbug\\dump' . '');
        \function_exists('\\Humbug\\dump' . '');
        \function_exists('var_dump' . '');
        \function_exists('\\var_dump' . '');
        \function_exists('Humbug\\var_dump' . '');
        \function_exists('\\Humbug\\var_dump' . '');

        PHP,
];
