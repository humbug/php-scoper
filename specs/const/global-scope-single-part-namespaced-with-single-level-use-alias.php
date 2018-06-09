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
        'title' => 'Single-level namespaced constant call in the global scope which is imported via an aliased use statement',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
    ],

    [
        'spec' => <<<'SPEC'
Constant call on an imported single-level namespace
- do not prefix the use statement (see tests related to single-level classes)
- prefix the constant call
- transform the call into a FQ call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    const DUMMY_CONST = '';
}

namespace {
    use Foo as A;
    
    A\DUMMY_CONST;
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\Foo;

const DUMMY_CONST = '';
namespace Humbug;

use Humbug\Foo as A;
\Humbug\Foo\DUMMY_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
FQ constant call on an imported single-level namespace
- do not prefix the use statement (see tests related to single-level classes)
- prefix the constant call
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace A {
    const DUMMY_CONST = '';
}

namespace {
    use Foo as A;
    
    \A\DUMMY_CONST;
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\A;

const DUMMY_CONST = '';
namespace Humbug;

use Humbug\Foo as A;
\Humbug\A\DUMMY_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Whitelisted onstant call on an imported single-level namespace
- do not prefix the use statement (see tests related to single-level classes)
- transform the call into a FQ call
SPEC
        ,
        'whitelist' => ['Foo\DUMMY_CONST'],
        'payload' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace Foo {
    const DUMMY_CONST = '';
}

namespace {
    use Foo as A;
    
    A\DUMMY_CONST;
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\Foo;

\define('Foo\\DUMMY_CONST', '');
namespace Humbug;

use Humbug\Foo as A;
\Foo\DUMMY_CONST;

PHP
    ],
];
