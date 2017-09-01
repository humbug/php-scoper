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
        'title' => 'Use statements',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Use statement of a class belonging to the global scope. Do nothing as this can belong to a PHP built-in class.
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Foo;

----
<?php

use Foo;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ use statement of a class belonging to the global scope. Do nothing as this can belong to a PHP built-in class.
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use \Foo;

----
<?php

use \Foo;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ use statement of a class belonging to the global scope. Do nothing as this can belong to a PHP built-in class.
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use \Foo;

----
<?php

use \Foo;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement of a (global) whitelisted class belonging to the global scope:
- prefix the use statement
- see `scope.inc.php` for the built-in global whitelisted classes
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use AppKernel;

----
<?php

use Humbug\AppKernel;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement of a (global) whitelisted class belonging to the global scope which is already whitelisted:
- do nothing
- see `scope.inc.php` for the built-in global whitelisted classes
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Humbug\AppKernel;

----
<?php

use Humbug\AppKernel;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement of two-level class:
- prefix the use statement
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Foo\Bar;

----
<?php

use Humbug\Foo\Bar;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement of two-level class which has been already prefixed:
- do nothing
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Humbug\Foo\Bar;

----
<?php

use Humbug\Foo\Bar;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Use statement of two-level class which has been whitelisted:
- prefix the use statement: only actual usage of the class will be whitelisted
SPEC
        ,
        'whitelist' => ['Foo\Bar'],
        'payload' => <<<'PHP'
<?php

use Foo\Bar;

----
<?php

use Humbug\Foo\Bar;

PHP
    ],
];
