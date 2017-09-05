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
        'title' => 'Class constant call of a class imported with an aliased use statement in a namespace',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace:
- prefix the namespace
- do not prefix the use statement (cf. class belonging to the global scope tests)
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

use Foo as X;

X::MAIN_CONST;
----
<?php

namespace Humbug\A;

use Foo as X;
\Foo::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ constant call on a aliased class which is imported via an aliased use statement and which belongs to the global namespace:
- prefix the namespace
- do not prefix the class (cf. class belonging to the global scope tests)
- resolve the alias
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

use Foo as X;

\X::MAIN_CONST;
----
<?php

namespace Humbug\A;

use Foo as X;
\Foo::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Constant call on a whitelisted class which is imported via an aliased use statement and which belongs to the global namespace:
- prefix the namespace
- prefix the use statement (cf. class belonging to the global scope tests and `scope.inc.php` for the built-in global whitelisted classes)
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

use AppKernel as X;

X::MAIN_CONST;
----
<?php

namespace Humbug\A;

use Humbug\AppKernel as X;
\Humbug\AppKernel::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ constant call on a whitelisted class which is imported via an aliased use statement and which belongs to the global namespace:
- prefix the namespace
- prefix the use statement (cf. class belonging to the global scope tests and `scope.inc.php` for the built-in global whitelisted classes)
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

use AppKernel as X;

\X::MAIN_CONST;
----
<?php

namespace Humbug\A;

use Humbug\AppKernel as X;
\Humbug\AppKernel::MAIN_CONST;

PHP
    ],
];
