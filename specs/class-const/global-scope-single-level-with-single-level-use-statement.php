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
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Constant call on a class which is imported via a use statement and which belongs to the global namespace' => [
        'payload' => <<<'PHP'
<?php

class Command {}

use Command;

Command::MAIN_CONST;
----
<?php

namespace Humbug;

class Command
{
}
use Humbug\Command;
\Humbug\Command::MAIN_CONST;

PHP
    ],

    'FQ constant call on a class which is imported via a use statement and which belongs to the global namespace' => [
        'payload' => <<<'PHP'
<?php

class Command {}

use Command;

\Command::MAIN_CONST;
----
<?php

namespace Humbug;

class Command
{
}
use Humbug\Command;
\Humbug\Command::MAIN_CONST;

PHP
    ],

    'Constant call on a whitelisted class which is imported via a use statement and which belongs to the global namespace' => [
        'payload' => <<<'PHP'
<?php

use Reflector;

Reflector::MAIN_CONST;
----
<?php

namespace Humbug;

use Reflector;
\Reflector::MAIN_CONST;

PHP
    ],

    'FQ constant call on a whitelisted class which is imported via a use statement and which belongs to the global namespace' => [
        'payload' => <<<'PHP'
<?php

use Reflector;

\Reflector::MAIN_CONST;
----
<?php

namespace Humbug;

use Reflector;
\Reflector::MAIN_CONST;

PHP
    ],
];
