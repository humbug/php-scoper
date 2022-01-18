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

        'expose-global-constants' => true,
        'expose-global-classes' => false,
        'expose-global-functions' => true,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'expected-recorded-classes' => [],
        'expected-recorded-functions' => [],
    ],

    'Constant call on a class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
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
Command::$mainStaticProp;

PHP
    ,

    'FQ constant call on a class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
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
    ,

    'Constant call on a whitelisted class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
<?php

use Reflector;

Reflector::$mainStaticProp;
----
<?php

namespace Humbug;

use Reflector;
Reflector::$mainStaticProp;

PHP
    ,

    'FQ constant call on a whitelisted class which is imported via a use statement and which belongs to the global namespace' => <<<'PHP'
<?php

use Reflector;

\Reflector::$mainStaticProp;
----
<?php

namespace Humbug;

use Reflector;
\Reflector::$mainStaticProp;

PHP
    ,
];
