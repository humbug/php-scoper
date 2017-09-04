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
        'title' => 'Namespaced function call statement in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Namespaced function call:
- prefix the namespace
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace X;

PHPUnit\main();
----
<?php

namespace Humbug\X;

\Humbug\X\PHPUnit\main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ namespaced function call:
- prefix the namespace
- prefix the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace X;

\PHPUnit\main();
----
<?php

namespace Humbug\X;

\Humbug\PHPUnit\main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Whitelisted namespaced function call:
- prefix the namespace
- prefix the call: whitelists only works on classes
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['PHPUnit\X\main'],
        'payload' => <<<'PHP'
<?php

namespace X;

PHPUnit\main();
----
<?php

namespace Humbug\X;

\Humbug\X\PHPUnit\main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ whitelisted namespaced function call:
- prefix the namespace
- prefix the call: whitelists only works on classes
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['PHPUnit\main'],
        'payload' => <<<'PHP'
<?php

namespace X;

\PHPUnit\main();
----
<?php

namespace Humbug\X;

\Humbug\PHPUnit\main();

PHP
    ],
];
