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
        'title' => 'Static method call statement of a namespaced class in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a namespaced class:
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

Foo\Bar::main();
----
<?php

\Humbug\Foo\Bar::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a namespaced class:
- prefix the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

\Foo\Bar::main();
----
<?php

\Humbug\Foo\Bar::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a namespaced class which has been whitelisted:
- do not prefix the call
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

Foo\Bar::main();
----
<?php

\Foo\Bar::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a namespaced class which has been whitelisted:
- do not prefix the call
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

\Foo\Bar::main();
----
<?php

\Foo\Bar::main();

PHP
    ],
];
