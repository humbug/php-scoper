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
        'title' => 'Global function call in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Global function call in the global scope
- do not prefix the call as may be part of PHP built-in functions
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

main();
----
<?php

\main();

PHP
    ],

    [
        'spec' => <<<'SPEC'
Global function call in the global scope
- do not prefix the call as may be part of PHP built-in functions
SPEC
        ,
        'payload' => <<<'PHP'
<?php

\main();
----
<?php

\main();

PHP
    ],
];
