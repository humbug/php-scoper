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
    ],

    <<<'PHP'
<?php

use A\{b};
use A\{B\c, d};
use \A\B\{C\d, e};

----
<?php

use Humbug\A\{b};
use Humbug\A\{B\c, d};
use Humbug\A\B\{C\d, e};

PHP
    ,
];
