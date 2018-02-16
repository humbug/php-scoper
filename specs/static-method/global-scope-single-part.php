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
        'title' => 'Static method call statement in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of a class belonging to the global namespace:
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Command {}

Command::main();
----
<?php

namespace Humbug;

class Command
{
}
\Humbug\Command::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of a class belonging to the global namespace:
- do not prefix the call as can be part of the global namespace
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Command {}

\Command::main();
----
<?php

namespace Humbug;

class Command
{
}
\Humbug\Command::main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Static method call statement of an internal class :
- do not prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

Closure::bind();
----
<?php

namespace Humbug;

\Closure::bind();

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ static method call statement of an internal class :
- do not prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

\Closure::bind();
----
<?php

namespace Humbug;

\Closure::bind();

PHP
    ],
];
