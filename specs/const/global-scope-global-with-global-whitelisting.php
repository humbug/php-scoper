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
        'title' => 'Global constant usage in the global scope with the global constants whitelisted',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Constant call in the global namespace' => <<<'PHP'
<?php

DUMMY_CONST;
----
<?php

namespace Humbug;

\DUMMY_CONST;

PHP
    ,

    'Whitelisted constant call in the global namespace' => <<<'PHP'
<?php

DUMMY_CONST;
----
<?php

namespace Humbug;

\DUMMY_CONST;

PHP
    ,

    'Constant call in the global namespace which is whitelisted' => [
        'whitelist' => ['\*'],
        'payload' => <<<'PHP'
<?php

DUMMY_CONST;
----
<?php

namespace {
    \DUMMY_CONST;
}

PHP
    ],

    'Internal constant call in the global namespace' => <<<'PHP'
<?php

DIRECTORY_SEPARATOR;
----
<?php

namespace Humbug;

\DIRECTORY_SEPARATOR;

PHP
    ,

    'FQ constant call in the global namespace' => <<<'PHP'
<?php

DUMMY_CONST;
----
<?php

namespace Humbug;

\DUMMY_CONST;

PHP
    ,

    'Global constant call in the global scope of a constant which has a use statement for a class importing a class with the same name' => <<<'PHP'
<?php

use Acme\Inf;

INF;
----
<?php

namespace Humbug;

use Humbug\Acme\Inf;
\INF;

PHP
    ,
];
