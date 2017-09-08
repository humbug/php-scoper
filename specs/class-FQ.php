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
        'title' => 'Class name resolution',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
    ],

    [
        'spec' => <<<'SPEC'
Different kind of whitelisted class constant calls in the global scope:
- do not prefix the use classes: they are all whitelisted
- transforms the call into a FQ
- resolve the aliases
SPEC
        ,
        'whitelist' => ['Foo\Bar', 'Foo\Bar\Poz'],
        'payload' => <<<'PHP'
<?php

use Foo as X;
use Foo\Bar as Y;
use Foo\Bar\Poz as Z;

Foo::MAIN_CONST;
X::MAIN_CONST;

Y::MAIN_CONST;
X\Bar::MAIN_CONST;
Foo\Bar::MAIN_CONST;

Z::MAIN_CONST;
Y\Poz::MAIN_CONST;
X\Bar\Poz::MAIN_CONST;
Foo\Bar\Poz::MAIN_CONST;
----
<?php

use Foo as X;
use Foo\Bar as Y;
use Foo\Bar\Poz as Z;
\Foo::MAIN_CONST;
\Foo::MAIN_CONST;
\Foo\Bar::MAIN_CONST;
\Foo\Bar::MAIN_CONST;
\Foo\Bar::MAIN_CONST;
\Foo\Bar\Poz::MAIN_CONST;
\Foo\Bar\Poz::MAIN_CONST;
\Foo\Bar\Poz::MAIN_CONST;
\Foo\Bar\Poz::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Different kind of class constant calls in the global scope:
- do not prefix the use classes: they are all whitelisted
- transforms the call into a FQ
- resolve the aliases
SPEC
        ,
        'payload' => <<<'PHP'
<?php

use Foo as X;
use Foo\Bar as Y;
use Foo\Bar\Poz as Z;

Foo::MAIN_CONST;
X::MAIN_CONST;

Y::MAIN_CONST;
X\Bar::MAIN_CONST;
Foo\Bar::MAIN_CONST;

Z::MAIN_CONST;
Y\Poz::MAIN_CONST;
X\Bar\Poz::MAIN_CONST;
Foo\Bar\Poz::MAIN_CONST;
----
<?php

use Foo as X;
use Humbug\Foo\Bar as Y;
use Humbug\Foo\Bar\Poz as Z;
\Foo::MAIN_CONST;
\Foo::MAIN_CONST;
\Humbug\Foo\Bar::MAIN_CONST;
\Humbug\Foo\Bar::MAIN_CONST;
\Humbug\Foo\Bar::MAIN_CONST;
\Humbug\Foo\Bar\Poz::MAIN_CONST;
\Humbug\Foo\Bar\Poz::MAIN_CONST;
\Humbug\Foo\Bar\Poz::MAIN_CONST;
\Humbug\Foo\Bar\Poz::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Different kind of whitelisted class constant calls in a namespace:
- do not prefix the use classes: they are all whitelisted
- transforms the call into a FQ
- resolve the aliases
SPEC
        ,
        'whitelist' => [
            'Foo\Bar',
            'Foo\Bar\Poz',

            'A\Foo',
            'A\Foo\Bar',
            'A\Foo\Bar\Poz',
            'A\Aoo',
            'A\Aoo\Aoz',
            'A\Aoz',
            'A\Aoo\Aoz\Poz',
        ],
        'payload' => <<<'PHP'
<?php

namespace A;

use Foo as X;
use Foo\Bar as Y;
use Foo\Bar\Poz as Z;

Aoo::MAIN_CONST;
Aoo\Aoz::MAIN_CONST;
Aoo\Aoz\Poz::MAIN_CONST;

Foo::MAIN_CONST;
X::MAIN_CONST;

Y::MAIN_CONST;
X\Bar::MAIN_CONST;
Foo\Bar::MAIN_CONST;

Z::MAIN_CONST;
Y\Poz::MAIN_CONST;
X\Bar\Poz::MAIN_CONST;
Foo\Bar\Poz::MAIN_CONST;
----
<?php

namespace Humbug\A;

use Foo as X;
use Foo\Bar as Y;
use Foo\Bar\Poz as Z;
\A\Aoo::MAIN_CONST;
\A\Aoo\Aoz::MAIN_CONST;
\A\Aoo\Aoz\Poz::MAIN_CONST;
\A\Foo::MAIN_CONST;
\Foo::MAIN_CONST;
\Foo\Bar::MAIN_CONST;
\Foo\Bar::MAIN_CONST;
\A\Foo\Bar::MAIN_CONST;
\Foo\Bar\Poz::MAIN_CONST;
\Foo\Bar\Poz::MAIN_CONST;
\Foo\Bar\Poz::MAIN_CONST;
\A\Foo\Bar\Poz::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Different kind of class constant calls in a namespace:
- do not prefix the use classes: they are all whitelisted
- transforms the call into a FQ
- resolve the aliases
SPEC
        ,
        'payload' => <<<'PHP'
<?php

namespace A;

use Foo as X;
use Foo\Bar as Y;
use Foo\Bar\Poz as Z;

Aoo::MAIN_CONST;
Aoo\Aoz::MAIN_CONST;
Aoo\Aoz\Poz::MAIN_CONST;

Foo::MAIN_CONST;
X::MAIN_CONST;

Y::MAIN_CONST;
X\Bar::MAIN_CONST;
Foo\Bar::MAIN_CONST;

Z::MAIN_CONST;
Y\Poz::MAIN_CONST;
X\Bar\Poz::MAIN_CONST;
Foo\Bar\Poz::MAIN_CONST;
----
<?php

namespace Humbug\A;

use Foo as X;
use Humbug\Foo\Bar as Y;
use Humbug\Foo\Bar\Poz as Z;
\Humbug\A\Aoo::MAIN_CONST;
\Humbug\A\Aoo\Aoz::MAIN_CONST;
\Humbug\A\Aoo\Aoz\Poz::MAIN_CONST;
\Humbug\A\Foo::MAIN_CONST;
\Foo::MAIN_CONST;
\Humbug\Foo\Bar::MAIN_CONST;
\Humbug\Foo\Bar::MAIN_CONST;
\Humbug\A\Foo\Bar::MAIN_CONST;
\Humbug\Foo\Bar\Poz::MAIN_CONST;
\Humbug\Foo\Bar\Poz::MAIN_CONST;
\Humbug\Foo\Bar\Poz::MAIN_CONST;
\Humbug\A\Foo\Bar\Poz::MAIN_CONST;

PHP
    ],

    [
        'spec' => <<<'SPEC'
Different kind of whitelisted class constant calls in multiple namespaces:
- do not prefix the use classes: they are all whitelisted
- transforms the call into a FQ
- resolve the aliases
SPEC
        ,
        'whitelist' => [
            'Foo\Bar',
            'Foo\Bar\Poz',

            'A\Foo',
            'A\Foo\Bar',
            'A\Foo\Bar\Poz',
            'A\Aoo',
            'A\Aoo\Aoz',
            'A\Aoz',
            'A\Aoo\Aoz\Poz',

            'B\Foo',
            'B\Foo\Bar',
            'B\Foo\Bar\Poz',
            'B\Aoo',
            'B\Aoo\Aoz',
            'B\Aoz',
            'B\Aoo\Aoz\Poz',
        ],
        'payload' => <<<'PHP'
<?php

namespace {
    use Foo as X;
    use Foo\Bar as Y;
    use Foo\Bar\Poz as Z;
    
    Foo::MAIN_CONST;
    X::MAIN_CONST;
    
    Y::MAIN_CONST;
    X\Bar::MAIN_CONST;
    Foo\Bar::MAIN_CONST;
    
    Z::MAIN_CONST;
    Y\Poz::MAIN_CONST;
    X\Bar\Poz::MAIN_CONST;
    Foo\Bar\Poz::MAIN_CONST;
}

namespace A {
    use Foo as X;
    use Foo\Bar as Y;
    use Foo\Bar\Poz as Z;
    
    Aoo::MAIN_CONST;
    Aoo\Aoz::MAIN_CONST;
    Aoo\Aoz\Poz::MAIN_CONST;
    
    Foo::MAIN_CONST;
    X::MAIN_CONST;
    
    Y::MAIN_CONST;
    X\Bar::MAIN_CONST;
    Foo\Bar::MAIN_CONST;
    
    Z::MAIN_CONST;
    Y\Poz::MAIN_CONST;
    X\Bar\Poz::MAIN_CONST;
    Foo\Bar\Poz::MAIN_CONST;
}

namespace B {
    use Foo as X;
    use Foo\Bar as Y;
    use Foo\Bar\Poz as Z;
    
    Aoo::MAIN_CONST;
    Aoo\Aoz::MAIN_CONST;
    Aoo\Aoz\Poz::MAIN_CONST;
    
    Foo::MAIN_CONST;
    X::MAIN_CONST;
    
    Y::MAIN_CONST;
    X\Bar::MAIN_CONST;
    Foo\Bar::MAIN_CONST;
    
    Z::MAIN_CONST;
    Y\Poz::MAIN_CONST;
    X\Bar\Poz::MAIN_CONST;
    Foo\Bar\Poz::MAIN_CONST;
}
----
<?php

namespace {
    use Foo as X;
    use Foo\Bar as Y;
    use Foo\Bar\Poz as Z;
    \Foo::MAIN_CONST;
    \Foo::MAIN_CONST;
    \Foo\Bar::MAIN_CONST;
    \Foo\Bar::MAIN_CONST;
    \Foo\Bar::MAIN_CONST;
    \Foo\Bar\Poz::MAIN_CONST;
    \Foo\Bar\Poz::MAIN_CONST;
    \Foo\Bar\Poz::MAIN_CONST;
    \Foo\Bar\Poz::MAIN_CONST;
}
namespace Humbug\A {
    use Foo as X;
    use Foo\Bar as Y;
    use Foo\Bar\Poz as Z;
    \A\Aoo::MAIN_CONST;
    \A\Aoo\Aoz::MAIN_CONST;
    \A\Aoo\Aoz\Poz::MAIN_CONST;
    \A\Foo::MAIN_CONST;
    \Foo::MAIN_CONST;
    \Foo\Bar::MAIN_CONST;
    \Foo\Bar::MAIN_CONST;
    \A\Foo\Bar::MAIN_CONST;
    \Foo\Bar\Poz::MAIN_CONST;
    \Foo\Bar\Poz::MAIN_CONST;
    \Foo\Bar\Poz::MAIN_CONST;
    \A\Foo\Bar\Poz::MAIN_CONST;
}
namespace Humbug\B {
    use Foo as X;
    use Foo\Bar as Y;
    use Foo\Bar\Poz as Z;
    \B\Aoo::MAIN_CONST;
    \B\Aoo\Aoz::MAIN_CONST;
    \B\Aoo\Aoz\Poz::MAIN_CONST;
    \B\Foo::MAIN_CONST;
    \Foo::MAIN_CONST;
    \Foo\Bar::MAIN_CONST;
    \Foo\Bar::MAIN_CONST;
    \B\Foo\Bar::MAIN_CONST;
    \Foo\Bar\Poz::MAIN_CONST;
    \Foo\Bar\Poz::MAIN_CONST;
    \Foo\Bar\Poz::MAIN_CONST;
    \B\Foo\Bar\Poz::MAIN_CONST;
}

PHP
    ],
];
