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
        'title' => 'New statement call of a namespaced class in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a class:
- prefix the namespace
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace X;

new Foo\Bar();
----
<?php

namespace Humbug\X;

new \Humbug\X\Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a class:
- prefix the namespace
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace X;

new \Foo\Bar();
----
<?php

namespace Humbug\X;

new \Humbug\Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a whitelisted class:
- prefix the namespace
- do not prefix the call
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['X\Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace X;

new Foo\Bar();
----
<?php

namespace Humbug\X;

new \X\Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a non-whitelisted class:
- prefix the namespace
- prefix the call
SPEC
        ,
        'whitelist' => ['X\Foo\Bar'],
        'payload' => <<<'PHP'
<?php

namespace X;

new \Foo\Bar();
----
<?php

namespace Humbug\X;

new \Humbug\Foo\Bar();

PHP
    ],
];
