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
        'title' => 'Use statements for constants',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Constant use statement for a constant belonging to the global namespace:
- prefix the use statement: as it is extremely rare to use a `use const` statement for a built-in const from the
global scope, we can relatively safely assume it is a user-land declare static-method which should be prefixed.
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use const FOO;

----
<?php

use const Humbug\FOO;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Constant use statement for a constant belonging to the global namespace and which has already been prefixed:
- do nothing
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use const Humbug\FOO;

----
<?php

use const Humbug\FOO;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Constant use statement for a namespaced constant:
- prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use const Foo\BAR;

----
<?php

use const Humbug\Foo\BAR;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Constant use statement for a namespaced constant which has already been prefixed:
- do nothing
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use const Humbug\Foo\BAR;

----
<?php

use const Humbug\Foo\BAR;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Constant use statement for a namespaced constant which has been whitelisted:
- prefix the use statement: whitelist is only for classes
SPEC
        ,
        'whitelist' => ['Foo\BAR'],
        'payload' => <<<'PHP'
<?php

use const Foo\BAR;

----
<?php

use const Humbug\Foo\BAR;

PHP
    ],
];
