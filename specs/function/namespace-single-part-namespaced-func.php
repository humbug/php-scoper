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
        'title' => 'single-part namespaced function call in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    'two-parts' => <<<'PHP'
<?php

namespace X;

PHPUnit\main();
----
<?php

namespace Humbug\X;

PHPUnit\main();

PHP
    ,

    'FQ two-parts' => <<<'PHP'
<?php

namespace X;

\PHPUnit\main();
----
<?php

namespace Humbug\X;

\Humbug\PHPUnit\main();

PHP
    ,

    // Whitelisting a function has no effect
    'whitelisted two-parts' => [
        'whitelist' => ['X\PHPUnit\main'],
        'payload' => <<<'PHP'
<?php

namespace X;

PHPUnit\main();
----
<?php

namespace Humbug\X;

PHPUnit\main();

PHP
    ],

    'FQ whitelisted two-parts' => [
        'whitelist' => ['PHPUnit\main'],
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
