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
        'title' => 'global function call in a namespace with single-level use statements and alias',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    // See tests for the use statements as to why we don't touch the use statement.
    // Won't do anything here as this class is part of the global namespace.
    'single-part' => <<<'PHP'
<?php

namespace A;

use function main as foo;

foo();
----
<?php

namespace Humbug\A;

use function Humbug\main as foo;

foo();

PHP
    ,

    'FQ single-part' => <<<'PHP'
<?php

namespace A;

use function main as foo;

\foo();
----
<?php

namespace Humbug\A;

use function Humbug\main as foo;

\foo();

PHP
    ,
];
