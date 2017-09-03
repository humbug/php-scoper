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
        'title' => 'Static method call statement in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a class:
- prefix the namespace
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

Foo::main();
----
<?php

namespace Humbug\A;

\Humbug\A\Foo::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a class belonging to the global namespace:
- prefix the namespace
- do not prefix the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

\Foo::main();
----
<?php

namespace Humbug\A;

\Foo::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a class belonging to the global namespace which has been whitelisted:
- prefix the namespace
- prefix the call
- See `scope.inc.php` for the built-in global whitelisted classes
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

\AppKernel::main();
----
<?php

namespace Humbug\A;

\Humbug\AppKernel::main();

PHP
    ],
];
