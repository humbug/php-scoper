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
        'title' => 'Single-level namespaced constant call in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => false,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Namespaced constant call' => <<<'PHP'
<?php

PHPUnit\DUMMY_CONST;
----
<?php

namespace Humbug;

\Humbug\PHPUnit\DUMMY_CONST;

PHP
    ,

    'FQ namespaced constant call' => <<<'PHP'
<?php

\PHPUnit\DUMMY_CONST;
----
<?php

namespace Humbug;

\Humbug\PHPUnit\DUMMY_CONST;

PHP
    ,

    'Namespaced constant call on a whitelisted constant' => [
        'whitelist' => ['PHPUnit\DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

PHPUnit\DUMMY_CONST;
----
<?php

namespace Humbug;

\PHPUnit\DUMMY_CONST;

PHP
    ],
];
