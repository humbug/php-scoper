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
        'title' => 'Global function call in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => false,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Global function call in a namespace' => <<<'PHP'
<?php

namespace A;

main();
----
<?php

namespace Humbug\A;

main();

PHP
    ,

    'Global FQ function call in a namespace' => <<<'PHP'
<?php

namespace A;

\main();
----
<?php

namespace Humbug\A;

\Humbug\main();

PHP
    ,
];
