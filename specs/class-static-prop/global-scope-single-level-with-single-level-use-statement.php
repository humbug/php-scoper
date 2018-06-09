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
        'title' => 'Class static property call of a class imported with a use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
    ],

    [
        'spec' => <<<'SPEC'
Constant call on a class which is imported via a use statement and which belongs to the global namespace:
- do not prefix the use statement (cf. class belonging to the global scope tests)
- transforms the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Command {}

use Command;

Command::$mainStaticProp;
----
<?php

namespace Humbug;

class Command
{
}
use Humbug\Command;
\Humbug\Command::$mainStaticProp;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ constant call on a class which is imported via a use statement and which belongs to the global namespace:
- do not prefix the use statement (cf. class belonging to the global scope tests)
- do nothing
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Command {}

use Command;

\Command::$mainStaticProp;
----
<?php

namespace Humbug;

class Command
{
}
use Humbug\Command;
\Humbug\Command::$mainStaticProp;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Constant call on a whitelisted class which is imported via a use statement and which belongs to the global namespace:
- transform the call in a FQ call (cf. class belonging to the global scope tests and `scope.inc.php` for the built-in
  global whitelisted classes)
SPEC
    ,
        'payload' => <<<'PHP'
<?php

use Reflector;

Reflector::$mainStaticProp;
----
<?php

namespace Humbug;

use Reflector;
\Reflector::$mainStaticProp;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ constant call on a whitelisted class which is imported via a use statement and which belongs to the global namespace:
- prefix the class (cf. class belonging to the global scope tests and `scope.inc.php` for the built-in global
  whitelisted classes)
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Reflector;

\Reflector::$mainStaticProp;
----
<?php

namespace Humbug;

use Reflector;
\Reflector::$mainStaticProp;

PHP
    ],
];
