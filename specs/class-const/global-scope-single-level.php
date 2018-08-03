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
        'title' => 'Class constant call in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Constant call on a class belonging to the global namespace' => [
        'payload' => <<<'PHP'
<?php

class Command {}

Command::MAIN_CONST;
----
<?php

namespace Humbug;

class Command
{
}
\Humbug\Command::MAIN_CONST;

PHP
    ],

    'Constant call on a class belonging to the global namespace which is whitelisted' => [
        'whitelist' => ['\*'],
        'payload' => <<<'PHP'
<?php

class Command {}

Command::MAIN_CONST;
----
<?php

namespace {
    class Command
    {
    }
    \Command::MAIN_CONST;
}

PHP
    ],

    'FQ constant call on a class belonging to the global namespace' => [
        'payload' => <<<'PHP'
<?php

class Command {}

\Command::MAIN_CONST;
----
<?php

namespace Humbug;

class Command
{
}
\Humbug\Command::MAIN_CONST;

PHP
    ],

    'Constant call on a whitelisted class belonging to the global namespace' => [
        'payload' => <<<'PHP'
<?php

Reflector::MAIN_CONST;
----
<?php

namespace Humbug;

\Reflector::MAIN_CONST;

PHP
    ],

    'FQ constant call on a whitelisted class belonging to the global namespace' => [
        'payload' => <<<'PHP'
<?php

\Reflector::MAIN_CONST;
----
<?php

namespace Humbug;

\Reflector::MAIN_CONST;

PHP
    ],
];
