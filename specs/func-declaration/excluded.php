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
        'title' => 'Function declarations in the global scope',
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
        'exclude-functions' => ['new_php20_function'],

        'expected-recorded-classes' => [],
        'expected-recorded-functions' => [
            ['new_php20_function', 'Humbug\new_php20_function'],
        ],
        'expected-recorded-ambiguous-functions' => [],
    ],

    'simple declaration' => <<<'PHP'
    <?php

    if (!function_exists('new_php20_function')) {
       function new_php20_function() {}
    }
    ----
    <?php

    namespace Humbug;

    if (!\function_exists('new_php20_function') && !\function_exists('Humbug\\new_php20_function')) {
        function new_php20_function()
        {
        }
    }

    PHP,

    'simple declaration with explicit equal comparison check' => <<<'PHP'
    <?php

    if (false == function_exists('new_php20_function')) {
       function new_php20_function() {}
    }
    ----
    <?php

    namespace Humbug;

    if (\false == \function_exists('new_php20_function') && !\function_exists('Humbug\\new_php20_function')) {
        function new_php20_function()
        {
        }
    }

    PHP,

    'simple inversed declaration with explicit equal comparison check' => <<<'PHP'
    <?php

    if (function_exists('new_php20_function') == false) {
       function new_php20_function() {}
    }
    ----
    <?php

    namespace Humbug;

    if (\function_exists('new_php20_function') == \false && !\function_exists('Humbug\\new_php20_function')) {
        function new_php20_function()
        {
        }
    }

    PHP,

    'simple declaration with explicit identical comparison check' => <<<'PHP'
    <?php

    if (false === function_exists('new_php20_function')) {
       function new_php20_function() {}
    }
    ----
    <?php

    namespace Humbug;

    if (\false === \function_exists('new_php20_function') && !\function_exists('Humbug\\new_php20_function')) {
        function new_php20_function()
        {
        }
    }

    PHP,

    'simple inversed declaration with explicit identical comparison check' => <<<'PHP'
    <?php

    if (function_exists('new_php20_function') === false) {
       function new_php20_function() {}
    }
    ----
    <?php

    namespace Humbug;

    if (\function_exists('new_php20_function') === \false && !\function_exists('Humbug\\new_php20_function')) {
        function new_php20_function()
        {
        }
    }

    PHP,

    'Already handled declaration' => <<<'PHP'
    <?php

    if (!function_exists('new_php20_function') && !function_exists('Humbug\new_php20_function')) {
       function new_php20_function() {}
    }
    ----
    <?php

    namespace Humbug;

    if (!\function_exists('new_php20_function') && !\function_exists('Humbug\\new_php20_function') && !\function_exists('Humbug\\new_php20_function')) {
        function new_php20_function()
        {
        }
    }

    PHP,

    'Non boolean not condition' => <<<'PHP'
    <?php

    if (function_exists('new_php20_function')) {
       function new_php20_function() {}
    }
    ----
    <?php

    namespace Humbug;

    if (\function_exists('new_php20_function')) {
        function new_php20_function()
        {
        }
    }

    PHP,

    'If condition is a BinaryOp_BooleanAnd; function exists is the left operand' => <<<'PHP'
    <?php

    if (!function_exists('new_php20_function') && PHP_VERSION_ID <= 80000) {
       function new_php20_function() {}
    }
    ----
    <?php

    namespace Humbug;

    if (!\function_exists('new_php20_function') && !\function_exists('Humbug\\new_php20_function') && \PHP_VERSION_ID <= 80000) {
        function new_php20_function()
        {
        }
    }

    PHP,

    'If condition is a BinaryOp_BooleanAnd; function exists is the right operand' => <<<'PHP'
    <?php

    if (PHP_VERSION_ID <= 80000 && !function_exists('new_php20_function')) {
       function new_php20_function() {}
    }
    ----
    <?php

    namespace Humbug;

    if (\PHP_VERSION_ID <= 80000 && (!\function_exists('new_php20_function') && !\function_exists('Humbug\\new_php20_function'))) {
        function new_php20_function()
        {
        }
    }

    PHP,

    'If condition is a BinaryOp_BooleanOr' => <<<'PHP'
    <?php

    if (!function_exists('new_php20_function') || PHP_VERSION_ID <= 80000) {
       function new_php20_function() {}
    }
    ----
    <?php

    namespace Humbug;

    if (!\function_exists('new_php20_function') && !\function_exists('Humbug\\new_php20_function') || \PHP_VERSION_ID <= 80000) {
        function new_php20_function()
        {
        }
    }

    PHP,
];
