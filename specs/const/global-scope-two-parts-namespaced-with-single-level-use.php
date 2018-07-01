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
        'title' => 'Two-parts namespaced constant call in the global scope with a single-level use statement',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => false,
        'whitelist-global-functions' => true,
    ],

    [
        'spec' => <<<'SPEC'
Namespaced constant call with namespace partially imported
- do not prefix the use statement (cf. tests related to global classes)
- prefix the call
- transform the call in a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Foo {}

use Foo;

Foo\Bar\DUMMY_CONST;
----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo;
\Humbug\Foo\Bar\DUMMY_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ namespaced constant call with namespace partially imported
- do not prefix the use statement (cf. tests related to global classes)
- prefix the call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

class Foo {}

use Foo;

\Foo\Bar\DUMMY_CONST;
----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo;
\Humbug\Foo\Bar\DUMMY_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Whitelisted namespaced constant call with namespace partially imported
- add prefixed namespace
- do not prefix the use statement (cf. tests related to global classes)
- transform the call in a FQ call
SPEC
        ,
        'whitelist' => ['Foo\Bar\DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

class Foo {}

use Foo;

Foo\Bar\DUMMY_CONST;
----
<?php

namespace Humbug;

class Foo
{
}
use Humbug\Foo;
\Foo\Bar\DUMMY_CONST;

PHP
    ],
];
