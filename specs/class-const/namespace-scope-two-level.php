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

        'expose-global-constants' => true,
        'expose-global-classes' => false,
        'expose-global-functions' => true,
        'expose-namespaces' => [],
        'expose-constants' => [],
        'expose-classes' => [],
        'expose-functions' => [],

        'exclude-namespaces' => [],
        'exclude-constants' => [],
        'exclude-classes' => [],
        'exclude-functions' => [],

        'expected-recorded-classes' => [],
        'expected-recorded-functions' => [],
    ],

    'Constant call on a namespaced class' => [
        'payload' => <<<'PHP'
<?php

namespace X\PHPUnit {
    class Command {}
}

namespace X {
    PHPUnit\Command::MAIN_CONST;
}
----
<?php

namespace Humbug\X\PHPUnit;

class Command
{
}
namespace Humbug\X;

PHPUnit\Command::MAIN_CONST;

PHP
    ],

    'FQ constant call on a namespaced class' => [
        'payload' => <<<'PHP'
<?php

namespace PHPUnit {
    class Command {}
}

namespace X {
    \PHPUnit\Command::MAIN_CONST;
}
----
<?php

namespace Humbug\PHPUnit;

class Command
{
}
namespace Humbug\X;

\Humbug\PHPUnit\Command::MAIN_CONST;

PHP
    ],

    'Constant call on a whitelisted namespaced class' => [
        'expose-classes' => ['X\PHPUnit\Command'],
        'expected-recorded-classes' => [
            ['X\PHPUnit\Command', 'Humbug\X\PHPUnit\Command'],
        ],
        'payload' => <<<'PHP'
<?php

namespace X\PHPUnit {
    class Command {}
}

namespace X {
    PHPUnit\Command::MAIN_CONST;
}
----
<?php

namespace Humbug\X\PHPUnit;

class Command
{
}
\class_alias('Humbug\\X\\PHPUnit\\Command', 'X\\PHPUnit\\Command', \false);
namespace Humbug\X;

PHPUnit\Command::MAIN_CONST;

PHP
    ],

    'Constant call on a namespaced class belonging to a whitelisted namespace' => [
        'whitelist' => ['X\PHPUnit\*'],
        'payload' => <<<'PHP'
<?php

namespace X\PHPUnit {
    class Command {}
}

namespace X {
    PHPUnit\Command::MAIN_CONST;
}
----
<?php

namespace X\PHPUnit;

class Command
{
}
namespace Humbug\X;

\X\PHPUnit\Command::MAIN_CONST;

PHP
    ],

    'Constant call on a namespaced class belonging to a whitelisted namespace (2)' => [
        'exclude-namespaces' => ['/^$/'],
        'payload' => <<<'PHP'
<?php

namespace X\PHPUnit {
    class Command {}
}

namespace X {
    PHPUnit\Command::MAIN_CONST;
}
----
<?php

namespace X\PHPUnit;

class Command
{
}
namespace X;

\X\PHPUnit\Command::MAIN_CONST;

PHP
    ],

    'FQ constant call on a whitelisted namespaced class' => [
        'expose-classes' => ['PHPUnit\Command'],
        'expected-recorded-classes' => [
            ['PHPUnit\Command', 'Humbug\PHPUnit\Command'],
        ],
        'payload' => <<<'PHP'
<?php

namespace PHPUnit {
    class Command {}
}

namespace X {
    \PHPUnit\Command::MAIN_CONST;
}
----
<?php

namespace Humbug\PHPUnit;

class Command
{
}
\class_alias('Humbug\\PHPUnit\\Command', 'PHPUnit\\Command', \false);
namespace Humbug\X;

\Humbug\PHPUnit\Command::MAIN_CONST;

PHP
    ],
];
