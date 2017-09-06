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
        'title' => 'New statement call of a namespaced class imported with a use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a namespaced class partially imported with a use statement:
- do not touch the use statement: see tests for the use statements as to why
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Foo;

new Foo\Bar();
----
<?php

use Foo;
new \Humbug\Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a namespaced class imported with a use statement:
- prefix the use statement
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Foo\Bar;

new Bar();
----
<?php

use Humbug\Foo\Bar;
new \Humbug\Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a namespaced class partially imported with a use statement:
- do not touch the use statement: see tests for the use statements and classes of the global namespace as to why
- prefix the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Foo;

new \Foo\Bar();
----
<?php

use Foo;
new \Humbug\Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a namespaced class imported with a use statement:
- prefix the use statement
- do not touch the call: see tests for the use statements and classes of the global namespace as to why
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Foo\Bar;

new \Bar();
----
<?php

use Humbug\Foo\Bar;
new \Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a whitelisted namespaced class partially imported with a use statement:
- do not touch the use statement: see tests for the use statements as to why
- do not prefix the call
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use Foo;

new Foo\Bar();
----
<?php

use Foo;
new \Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a whitelisted namespaced class imported with a use statement:
- prefix the use statement
- do not prefix the call
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use Foo\Bar;

new Bar();
----
<?php

use Foo\Bar;
new \Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a whitelisted namespaced class partially imported with a use statement:
- do not touch the use statement: see tests for the use statements as to why
- do not prefix the call
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use Foo;

new \Foo\Bar();
----
<?php

use Foo;
new \Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a whitelisted namespaced class imported with a use statement:
- prefix the use statement
- do not prefix the call
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use Foo\Bar;

new \Bar();
----
<?php

use Foo\Bar;
new \Bar();

PHP
    ],
];
