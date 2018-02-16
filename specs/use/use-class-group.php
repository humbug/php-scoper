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
        'title' => 'Use statements for classes with group use statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Multiple group use statement:
- transform grouped statements into simple statements
- prefix each of them
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use A\{B};
use A\{B\C, D};
use \A\B\{C\D as ABCD, E};

----
<?php

namespace Humbug;

use Humbug\A\B;
use Humbug\A\B\C;
use Humbug\A\D;
use Humbug\A\B\C\D as ABCD;
use Humbug\A\B\E;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Multiple group use statement which are already prefixed:
- transform grouped statements into simple statements
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Humbug\A\{B};
use Humbug\A\{B\C, D};
use \Humbug\A\B\{C\D, E};

----
<?php

namespace Humbug;

use Humbug\A\B;
use Humbug\A\B\C;
use Humbug\A\D;
use Humbug\A\B\C\D;
use Humbug\A\B\E;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Multiple group use statement with whitelisted classes:
- transform grouped statements into simple statements
- prefix each of them: only actual usages will be whitelisted, not the use statements
SPEC
        ,
        'whitelist' => [
            'A\B',
            'A\B\C',
        ],
        'payload' => <<<'PHP'
<?php

use A\{B};
use A\{B\C, D};
use \A\B\{C\D, E};

----
<?php

namespace Humbug;

use A\B;
use A\B\C;
use Humbug\A\D;
use Humbug\A\B\C\D;
use Humbug\A\B\E;

PHP
    ],
];
