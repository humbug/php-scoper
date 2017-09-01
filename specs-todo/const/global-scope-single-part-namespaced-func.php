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
        'title' => 'single-part namespaced constant reference in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As there is no namespaced PHP core functions, we can safely prefix.
    'two-parts' => <<<'PHP'
<?php

PHPUnit\DUMMY_CONST;
----
<?php

\Humbug\PHPUnit\DUMMY_CONST;

PHP
    ,

    // As there is no namespaced PHP core functions, we can safely prefix.
    'FQ two-parts' => <<<'PHP'
<?php

\PHPUnit\DUMMY_CONST;
----
<?php

\Humbug\PHPUnit\DUMMY_CONST;

PHP
    ,

    // Whitelisting a constant has no effect
    'whitelisted two-parts' => [
        'whitelist' => ['PHPUnit\DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

PHPUnit\DUMMY_CONST;
----
<?php

\Humbug\PHPUnit\DUMMY_CONST;

PHP
    ],

    'FQ whitelisted two-parts' => [
        'whitelist' => ['PHPUnit\DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

\PHPUnit\DUMMY_CONST;
----
<?php

\Humbug\PHPUnit\DUMMY_CONST;

PHP
    ],
];
