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
        'title' => 'Class constant call of a namespaced class in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Constant call on a namespaced class:
- prefix the namespace
- prefix the class
- transforms the call into a FQ call to avoid autoloading issues
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace X;

PHPUnit\Command::MAIN_CONST;
----
<?php

namespace Humbug\X;

\Humbug\PHPUnit\Command::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ constant call on a namespaced class:
- prefix the namespace
- prefix the class
- transforms the call into a FQ call to avoid autoloading issues
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace X;

\PHPUnit\Command::MAIN_CONST;
----
<?php

namespace Humbug\X;

\Humbug\PHPUnit\Command::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Constant call on a whitelisted namespaced class:
- prefix the namespace
- do not prefix the class
- transforms the call into a FQ call to avoid autoloading issues
SPEC
        ,
        'whitelist' => ['PHPUnit\Command'],
        'payload' => <<<'PHP'
<?php

namespace X;

PHPUnit\Command::MAIN_CONST;
----
<?php

namespace Humbug\X;

\PHPUnit\Command::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ constant call on a whitelisted namespaced class:
- prefix the namespace
- do not prefix the class
- transforms the call into a FQ call to avoid autoloading issues
SPEC
        ,
        'whitelist' => ['PHPUnit\Command'],
        'payload' => <<<'PHP'
<?php

namespace X;

\PHPUnit\Command::MAIN_CONST;
----
<?php

namespace Humbug\X;

\PHPUnit\Command::MAIN_CONST;

PHP
    ],
];
