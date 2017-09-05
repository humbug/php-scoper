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
        'title' => 'Class constant call of a class imported with a use statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
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

use Command;

Command::MAIN_CONST;
----
<?php

use Command;
\Command::MAIN_CONST;

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

use Command;

\Command::MAIN_CONST;
----
<?php

use Command;
\Command::MAIN_CONST;

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

use AppKernel;

AppKernel::MAIN_CONST;
----
<?php

use Humbug\AppKernel;
\Humbug\AppKernel::MAIN_CONST;

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

use AppKernel;

\AppKernel::MAIN_CONST;
----
<?php

use Humbug\AppKernel;
\Humbug\AppKernel::MAIN_CONST;

PHP
    ],
];
