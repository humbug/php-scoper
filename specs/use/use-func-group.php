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
        'title' => 'Use statements for functions with group statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    <<<'PHP'
<?php

use A\{b};
use A\{B\c, d};
use \A\B\{C\g, e};

----
<?php

namespace Humbug;

use Humbug\A\b;
use Humbug\A\B\c;
use Humbug\A\d;
use Humbug\A\B\C\g;
use Humbug\A\B\e;

PHP
    ,
];
