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
        'title' => 'Global constant usage in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'expose-global-constants' => false,
        'expose-global-classes' => false,
        'expose-global-functions' => true,
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Constant call in a namespace' => <<<'PHP'
<?php

namespace A;

DUMMY_CONST;
----
<?php

namespace Humbug\A;

DUMMY_CONST;

PHP
    ,

    'Whitelisted constant call in a namespace' => [
        'whitelist' => ['DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

namespace A;

DUMMY_CONST;
----
<?php

namespace Humbug\A;

DUMMY_CONST;

PHP
    ],

    'FQ constant call in a namespace' => <<<'PHP'
<?php

namespace A;

\DUMMY_CONST;
----
<?php

namespace Humbug\A;

\Humbug\DUMMY_CONST;

PHP
    ,

    'Whitelisted FQ constant call in a namespace' => [
        'whitelist' => ['DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

namespace A;

\DUMMY_CONST;
----
<?php

namespace Humbug\A;

\DUMMY_CONST;

PHP
    ],
];
