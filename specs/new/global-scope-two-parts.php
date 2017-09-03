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
        'title' => 'New statement call of a namespaced class in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a namespaced class:
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

new Foo\Bar();
----
<?php

new \Humbug\Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a namespaced class:
- prefix the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

new \Foo\Bar();
----
<?php

new \Humbug\Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
New statement call of a namespaced class which has been whitelisted:
- do not prefix the call
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

new Foo\Bar();
----
<?php

new \Foo\Bar();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ new statement call of a namespaced class which has been whitelisted:
- do not prefix the call
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

new \Foo\Bar();
----
<?php

new \Foo\Bar();

PHP
    ],
];
