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
        'title' => 'Excerpts of code used for executable PHP files (e.g. for PHPUnit)',
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

    'Some statements made directly in the global namespace' => <<<'PHP'
        <?php declare(strict_types=1);

        if (\true) {
            echo "yo";
        }

        if (\false) {
            echo "oy";
        }

        ----
        <?php

        declare (strict_types=1);
        namespace Humbug;

        if (\true) {
            echo "yo";
        }
        if (\false) {
            echo "oy";
        }

        PHP,

    'Some statements made directly in the global namespace with a shebang' => <<<'PHP'
        #!/usr/bin/env php
        <?php declare(strict_types=1);

        if (\true) {
            echo "yo";
        }

        if (\false) {
            echo "oy";
        }

        ----
        #!/usr/bin/env php
        <?php
        declare (strict_types=1);
        namespace Humbug;

        if (\true) {
            echo "yo";
        }
        if (\false) {
            echo "oy";
        }

        PHP,
];
