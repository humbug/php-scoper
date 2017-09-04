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
        'title' => 'New statement call in a namespace',
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

namespace A;

new Foo();
----
<?php

namespace Humbug\A;

new \Humbug\A\Foo();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a class belonging to the global namespace:
- prefix the namespace
- do not prefix the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

new \Foo();
----
<?php

namespace Humbug\A;

new \Foo();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a class belonging to the global namespace which has been whitelisted:
- prefix the namespace
- prefix the call
- See `scope.inc.php` for the built-in global whitelisted classes
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

new \AppKernel();
----
<?php

namespace Humbug\A;

new \Humbug\AppKernel();

PHP
    ],
];
