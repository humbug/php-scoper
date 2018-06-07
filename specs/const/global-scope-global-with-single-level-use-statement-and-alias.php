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
        'title' => 'Global constant imported with an aliased use statement used in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Constant call imported with an aliased use statement:
- prefix the use statement
- prefix the call
- transforms the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use const DUMMY_CONST as FOO;

FOO;
----
<?php

namespace Humbug;

use const Humbug\DUMMY_CONST as FOO;
\Humbug\DUMMY_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Whitelisted constant call imported with an aliased use statement:
- add prefixed namespace
- transforms the call into a FQ call
SPEC
        ,
        'whitelist' => ['DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

use const DUMMY_CONST as FOO;

FOO;
----
<?php

namespace Humbug;

use const DUMMY_CONST as FOO;
\DUMMY_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Constant call imported with an aliased use statement:
- prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use const DUMMY_CONST as FOO;

\FOO;
----
<?php

namespace Humbug;

use const Humbug\DUMMY_CONST as FOO;
\Humbug\FOO;

PHP
    ],
];
