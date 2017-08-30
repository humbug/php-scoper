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
        'title' => 'global constant reference in the global scope with single-level use statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // As it is extremely rare to use a `use constant` statement for a built-in constant from the
    // global scope, we can relatively safely assume it is a user-land declared constant which should
    // be prefixed.
    'single-part' => <<<'PHP'
<?php

use constant DUMMY_CONST;

DUMMY_CONST;
----
<?php

use constant Humbug\DUMMY_CONST;

DUMMY_CONST;

PHP
    ,

    // As it is extremely rare to use a `use constant` statement for a built-in constant from the
    // global scope, we can relatively safely assume it is a user-land declared constant which should
    // be prefixed.
    'FQ single-part' => <<<'PHP'
<?php

use constant DUMMY_CONST;

\DUMMY_CONST;
----
<?php

use constant Humbug\DUMMY_CONST;

\DUMMY_CONST;

PHP
    ,
];
