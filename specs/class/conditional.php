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
        'title' => 'Conditional class declaration',
        // Default values. If not specified will be the one used
        'prefix' => 'Humbug',
        'whitelist' => [],
        'whitelist-global-constants' => true,
        'whitelist-global-classes' => false,
        'whitelist-global-functions' => true,
        'registered-classes' => [],
        'registered-functions' => [],
    ],

    'Declaration in the global namespace' => <<<'PHP'
<?php

if (true) {
    class A {}
}
----
<?php

namespace Humbug;

if (\true) {
    class A
    {
    }
}

PHP
    ,

    'Declaration of a whitelisted class in the global namespace' => [
        'whitelist' => ['A'],
        'registered-classes' => [
            ['A', 'Humbug\A'],
        ],
        'payload' => <<<'PHP'
<?php

if (true) {
    class A {}
}
----
<?php

namespace Humbug;

if (\true) {
    class A
    {
    }
}

PHP
    ],

    'Declaration in a namespace' => <<<'PHP'
<?php

namespace Foo;

if (true) {
    class A {}
}
----
<?php

namespace Humbug\Foo;

if (\true) {
    class A
    {
    }
}

PHP
    ,

    'Declaration of a whitelisted class' => [
        'whitelist' => ['Foo\A'],
        'registered-classes' => [
            ['Foo\A', 'Humbug\Foo\A'],
        ],
        'payload' => <<<'PHP'
<?php

namespace Foo;

if (true) {
    class A {}
}
----
<?php

namespace Humbug\Foo;

if (\true) {
    class A
    {
    }
}

PHP
    ],

    'Multiple declarations in different namespaces' => <<<'PHP'
<?php

namespace X {
    if (true) {
        class A {}
    }
}

namespace Y {
    if (true) {
        class B {}
    }
}

namespace Z {
    if (true) {
        class C {}
    }
}
----
<?php

namespace Humbug\X;

if (\true) {
    class A
    {
    }
}
namespace Humbug\Y;

if (\true) {
    class B
    {
    }
}
namespace Humbug\Z;

if (\true) {
    class C
    {
    }
}

PHP
    ,
];
