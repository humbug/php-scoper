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
        'title' => 'Global function call imported with a use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Global function call imported with a use statement in the global scope
- prefix the use statement
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use function main;

main();
----
<?php

namespace Humbug;

use function Humbug\main;
\Humbug\main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Global function call imported with a use statement in the global scope
- prefix the use statement
- prefix the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use function main;

\main();
----
<?php

namespace Humbug;

use function Humbug\main;
\Humbug\main();

PHP
    ],
];
