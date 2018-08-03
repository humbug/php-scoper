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
        'title' => 'Native function calls',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Internal function in a namespace' => <<<'PHP'
<?php

namespace Acme;

$x = is_array([]);

----
<?php

namespace Humbug\Acme;

$x = \is_array([]);

PHP
    ,

    'Namespaced function having the same name as an internal function' => <<<'PHP'
<?php

namespace Acme;

use function Acme\is_array;

$x = is_array([]);

----
<?php

namespace Humbug\Acme;

use function Humbug\Acme\is_array;
$x = \Humbug\Acme\is_array([]);

PHP
    ,
];
