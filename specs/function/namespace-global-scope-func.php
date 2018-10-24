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
        'title' => 'global function call in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => false,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    // We don't do anything as there is no ways to distinguish between a namespaced function call
    // from the same namespace and a function registered in the global scope
    'single-part' => <<<'PHP'
<?php

namespace X;

main();
----
<?php

namespace Humbug\X;

main();

PHP
    ,

    'FQ single-part' => <<<'PHP'
<?php

namespace X;

\main();
----
<?php

namespace Humbug\X;

\Humbug\main();

PHP
    ,
];
