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
        'whitelist-global-constants' => true,
    ],

    [
        'spec' => <<<'SPEC'
Constant call in a namespace:
- prefix the namespace
- do nothing: the constant can either belong to the same namespace or the global namespace
SPEC
        ,
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

    [
        'spec' => <<<'SPEC'
Whitelisted constant call in a namespace:
- prefix the namespace
- do nothing: the constant can either belong to the same namespace or the global namespace
SPEC
        ,
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

    [
        'spec' => <<<'SPEC'
FQ constant call in a namespace:
- prefix the namespace
- prefix the constant call
SPEC
    ,
        'payload' => <<<'PHP'
<?php

namespace A;

\DUMMY_CONST;
----
<?php

namespace Humbug\A;

\Humbug\DUMMY_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Whitelisted FQ constant call in a namespace:
- prefix the namespace
- prefix the constant call
SPEC
    ,
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
