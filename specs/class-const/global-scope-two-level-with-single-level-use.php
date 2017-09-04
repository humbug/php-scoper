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
        'title' => 'Class constant call of a namespaced class imported with a use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Constant call on a namespaced class partially imported with a use statement:
- prefix the class only (not the use statement)
- transforms the call into a FQ call to avoid autoloading issues
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Foo;

Foo\Bar::MAIN_CONST;
----
<?php

use Foo;

\Humbug\Foo\Bar::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Constant call on a namespaced class imported with a use statement:
- prefix the use statement
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Foo\Bar;

Bar\X::MAIN_CONST;
----
<?php

use Humbug\Foo\Bar;

\Humbug\Foo\Bar\X::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ constant call on a namespaced class imported with a use statement:
- prefix the class only (not the use statement)
- transforms the call into a FQ call to avoid autoloading issues
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Foo;

\Foo\Bar::MAIN_CONST;
----
<?php

use Foo;

\Humbug\Foo\Bar::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ Constant call on a whitelisted namespaced class partially imported with a use statement:
- do not prefix the class neither the use statement
- transforms the call into a FQ call to avoid autoloading issues
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use Foo;

Foo\Bar::MAIN_CONST;
----
<?php

use Foo;

\Foo\Bar::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ constant call on a whitelisted namespaced class imported with a use statement:
- prefix the class only (not the use statement)
- transforms the call into a FQ call to avoid autoloading issues
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use Foo;

\Foo\Bar::MAIN_CONST;
----
<?php

use Foo;

\Foo\Bar::MAIN_CONST;

PHP
    ],
];
