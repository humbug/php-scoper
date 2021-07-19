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
        'title' => 'Global function call imported with an aliased use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'exclude-namespaces' => [],
        'expose-global-constants' => true,
        'expose-global-classes' => false,
        'expose-global-functions' => false,
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Global function call imported with a use statement in the global scope' => <<<'PHP'
<?php

use function main as foo;

foo();
----
<?php

namespace Humbug;

use function Humbug\main as foo;
foo();

PHP
    ,

    'Global function call imported with a use statement in the global scope with global functions whitelisted' => [
        'expose-global-functions' => true,
        'registered-functions' => [
            ['main', 'Humbug\main'],
        ],
        'payload' => <<<'PHP'
<?php

use function main as foo;

foo();
----
<?php

namespace Humbug;

use function Humbug\main as foo;
foo();

PHP
    ],

    'Global FQ function call imported with a use statement in the global scope' => <<<'PHP'
<?php

use function main as foo;

\foo();
----
<?php

namespace Humbug;

use function Humbug\main as foo;
\Humbug\foo();

PHP
    ,

    'Global FQ function call imported with a use statement in the global scope with global functions whitelisted' => [
        'expose-global-functions' => true,
        'registered-functions' => [
            ['foo', 'Humbug\foo'],
        ],
        'payload' => <<<'PHP'
<?php

use function main as foo;

\foo();
----
<?php

namespace Humbug;

use function Humbug\main as foo;
\Humbug\foo();

PHP
    ],
];
