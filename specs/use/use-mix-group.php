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
        'title' => 'Mixed use statements with group statements',
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

use A\B\{C\D, function b\c, const D};

D::class;
c();
D;

----
<?php

namespace Humbug;

use Humbug\A\B\C\D;
use function Humbug\A\B\b\c;
use const Humbug\A\B\D;
\Humbug\A\B\C\D::class;
\Humbug\A\B\b\c();
\Humbug\A\B\D;

PHP
    ,
];
