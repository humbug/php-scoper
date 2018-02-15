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
        'title' => 'Two-levels namespaced constant call in the global scope',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Namespaced constant call
- prefix the call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

PHPUnit\Command\DUMMY_CONST;
----
<?php

namespace Humbug;

\Humbug\PHPUnit\Command\DUMMY_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ namespaced constant call
- prefix the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

\PHPUnit\Command\DUMMY_CONST;
----
<?php

namespace Humbug;

\Humbug\PHPUnit\Command\DUMMY_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Namespaced constant call on a whitelisted constant
- prefix the call: the whitelist only works for classes
SPEC
        ,
        'whitelist' => ['PHPUnit\DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

PHPUnit\Command\DUMMY_CONST;
----
<?php

namespace Humbug;

\Humbug\PHPUnit\Command\DUMMY_CONST;

PHP
    ],
];
