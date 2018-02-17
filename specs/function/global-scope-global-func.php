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
        'title' => 'Global function call in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Global function call in the global scope
- prefix the function
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

main();
----
<?php

namespace Humbug;

\Humbug\main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ global function call in the global scope
- prefix the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

\main();
----
<?php

namespace Humbug;

\Humbug\main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Global function call in the global scope of an internal function
- do not prefix the function
SPEC
        ,
        'payload' => <<<'PHP'
<?php

is_array();
----
<?php

namespace Humbug;

\is_array();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ global function call in the global scope of an internal function
- do not prefix the function
SPEC
        ,
        'payload' => <<<'PHP'
<?php

\is_array();
----
<?php

namespace Humbug;

\is_array();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Global function call in the global scope of a function which has a use statement for a class importing a class with the
same name
- do not prefix the function
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Acme\Glob;

glob();
----
<?php

namespace Humbug;

use Humbug\Acme\Glob;
\glob();

PHP
    ],
];
