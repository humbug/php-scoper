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
        'title' => 'single-part namespaced function call in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As there is no namespaced PHP core functions, we can safely prefix.
    'two-parts' => <<<'PHP'
<?php

PHPUnit\main();
----
<?php

\Humbug\PHPUnit\main();

PHP
    ,

    // As there is no namespaced PHP core functions, we can safely prefix.
    'FQ two-parts' => <<<'PHP'
<?php

\PHPUnit\main();
----
<?php

\Humbug\PHPUnit\main();

PHP
    ,

    // Whitelisting a function has no effect
    'whitelisted two-parts' => [
        'whitelist' => ['PHPUnit\main'],
        'payload' => <<<'PHP'
<?php

PHPUnit\main();
----
<?php

\Humbug\PHPUnit\main();

PHP
    ],

    'FQ whitelisted two-parts' => [
        'whitelist' => ['PHPUnit\main'],
        'payload' => <<<'PHP'
<?php

\PHPUnit\main();
----
<?php

\Humbug\PHPUnit\main();

PHP
    ],
];
