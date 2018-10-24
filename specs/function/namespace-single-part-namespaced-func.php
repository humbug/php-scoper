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
        'title' => 'Namespaced function call statement in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => false,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Namespaced function call' => <<<'PHP'
<?php

namespace X;

PHPUnit\main();
----
<?php

namespace Humbug\X;

\Humbug\X\PHPUnit\main();

PHP
    ,

    'FQ namespaced function call' => <<<'PHP'
<?php

namespace X;

\PHPUnit\main();
----
<?php

namespace Humbug\X;

\Humbug\PHPUnit\main();

PHP
    ,

    'Whitelisted namespaced function call' => [
        'whitelist' => ['PHPUnit\X\main'],
        // No function registered to the whitelist here since no FQ could be resolved
        'payload' => <<<'PHP'
<?php

namespace X;

PHPUnit\main();
----
<?php

namespace Humbug\X;

\Humbug\X\PHPUnit\main();

PHP
    ],

    'FQ whitelisted namespaced function call' => [
        'whitelist' => ['PHPUnit\main'],
        'registered-functions' => [
            ['PHPUnit\main', 'Humbug\PHPUnit\main'],
        ],
        'payload' => <<<'PHP'
<?php

namespace X;

\PHPUnit\main();
----
<?php

namespace Humbug\X;

\Humbug\PHPUnit\main();

PHP
    ],
];
