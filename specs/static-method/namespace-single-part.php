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
        'title' => 'Static method call statement in a namespace',
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

    'Static method call statement of a class' => <<<'PHP'
<?php

namespace A;

class Foo {}

Foo::main();
----
<?php

namespace Humbug\A;

class Foo
{
}
Foo::main();

PHP
    ,

    'FQ static method call statement of a class belonging to the global namespace' => <<<'PHP'
<?php

namespace {
    class Foo {}
}

namespace A {
    \Foo::main();
}
----
<?php

namespace Humbug;

class Foo
{
}
namespace Humbug\A;

\Humbug\Foo::main();

PHP
    ,

    'FQ static method call statement of a class belonging to the global namespace which has been whitelisted' => <<<'PHP'
<?php

namespace A;

\Closure::bind();
----
<?php

namespace Humbug\A;

\Closure::bind();

PHP
    ,
];
