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
        'title' => 'Class constant call in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
    ],

    [
        'spec' => <<<'SPEC'
Constant call on a class belonging to the global namespace or the current namespace:
- prefix the namespace
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace X;

class Command {}

Command::MAIN_CONST;
----
<?php

namespace Humbug\X;

class Command
{
}
\Humbug\X\Command::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ constant call on a class belonging to the global namespace or the current namespace:
- prefix the namespace
- do not touch the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Command {}
}

namespace X {
    \Command::MAIN_CONST;
}
----
<?php

namespace Humbug;

class Command
{
}
namespace Humbug\X;

\Humbug\Command::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Constant call on a whitelisted class belonging to the global namespace:
- prefix the namespace
- transforms the call into a FQ call to avoid autoloading issues
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace X;

use Reflector;

Reflector::MAIN_CONST;
----
<?php

namespace Humbug\X;

use Reflector;
\Reflector::MAIN_CONST;

PHP
    ],
];
