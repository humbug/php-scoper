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
        'title' => 'Use statements for functions',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
    ],

    [
        'spec' => <<<'SPEC'
Use statement for a function belonging to the global namespace:
- wrap the code in a prefixed namespace
- prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use function foo;

----
<?php

namespace Humbug;

use function Humbug\foo;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement for an internal function belonging to the global namespace:
- wrap the code in a prefixed namespace
- do not prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use function is_array;

----
<?php

namespace Humbug;

use function is_array;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement for a function belonging to the global namespace which has already been prefixed:
- do nothing
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use function Humbug\foo;

----
<?php

namespace Humbug;

use function Humbug\foo;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement for a namespaced function:
- prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use function Foo\bar;

----
<?php

namespace Humbug;

use function Humbug\Foo\bar;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement for a namespaced function which has already been prefixed:
- do nothing
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use function Humbug\Foo\bar;

----
<?php

namespace Humbug;

use function Humbug\Foo\bar;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement for a namespaced function which has been whitelisted:
- prefix the use statement: the whitelist only works for classes
SPEC
        ,
        'whitelist' => ['Foo\bar'],
        'payload' => <<<'PHP'
<?php

use function Foo\bar;

----
<?php

namespace Humbug;

use function Humbug\Foo\bar;

PHP
    ],
];
