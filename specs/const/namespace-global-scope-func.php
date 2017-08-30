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
        'title' => 'global constant reference in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // We don't do anything as there is no ways to distinguish between a namespaced constant reference
    // from the same namespace and a function registered in the global scope
    'single-part' => <<<'PHP'
<?php

namespace X;

DUMMY_CONST;
----
<?php

namespace Humbug\X;

DUMMY_CONST;

PHP
    ,

    'FQ single-part' => <<<'PHP'
<?php

namespace X;

\DUMMY_CONST;
----
<?php

namespace Humbug\X;

\DUMMY_CONST;

PHP
    ,
];
